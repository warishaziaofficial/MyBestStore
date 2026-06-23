<?php

namespace Cms\Support;

use Cms\Models\Order;
use Cms\Models\OrderItem;
use Cms\Models\Product;
use Cms\Models\SocialAccount;
use Cms\Models\SocialSyncLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SocialOrderImporter
{
    /**
     * Import a social-platform order into Orders + OrderItems.
     *
     * @param  array<string, mixed>  $payload
     * @return array{status: string, order?: Order, message: string, log: SocialSyncLog}
     */
    public static function import(array $payload, string $triggerType = 'webhook'): array
    {
        self::ensureTables();

        $data = self::validatePayload($payload);
        $platform = $data['platform'];
        $externalOrderId = $data['external_order_id'];

        $account = self::resolveAccount($data);
        $accountId = $account?->id;

        $existing = Order::query()
            ->where('source', $platform)
            ->where('external_order_id', $externalOrderId)
            ->first();

        if ($existing) {
            $log = self::writeLog([
                'social_account_id' => $accountId,
                'platform' => $platform,
                'trigger_type' => $triggerType,
                'status' => 'duplicate',
                'orders_imported' => 0,
                'external_order_id' => $externalOrderId,
                'order_id' => $existing->id,
                'payload' => $data,
                'message' => 'Order already imported from this platform.',
            ]);

            return [
                'status' => 'duplicate',
                'order' => $existing,
                'message' => 'Order already exists for this social platform reference.',
                'log' => $log,
            ];
        }

        try {
            $order = DB::transaction(function () use ($data, $platform, $account) {
                $lines = self::buildLineItems($data['items']);
                $subtotal = array_sum(array_column($lines, 'line_total'));
                $shipping = (int) ($data['shipping'] ?? 0);
                $total = $subtotal + $shipping;

                $order = Order::create([
                    'customer_id' => $data['customer_id'] ?? null,
                    'order_number' => self::generateOrderNumber($platform),
                    'customer_name' => $data['customer_name'],
                    'customer_email' => $data['customer_email'],
                    'customer_phone' => $data['customer_phone'] ?? null,
                    'source' => $platform,
                    'external_order_id' => $data['external_order_id'],
                    'external_account_id' => $data['external_account_id'] ?? $account?->account_id,
                    'source_metadata' => $data['source_metadata'] ?? null,
                    'status' => $data['status'] ?? 'pending',
                    'payment_status' => $data['payment_status'] ?? 'pending',
                    'subtotal' => $subtotal,
                    'shipping' => $shipping,
                    'total' => $total,
                    'notes' => $data['notes'] ?? null,
                ]);

                foreach ($lines as $line) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $line['product_id'],
                        'product_name' => $line['product_name'],
                        'quantity' => $line['quantity'],
                        'unit_price' => $line['unit_price'],
                        'line_total' => $line['line_total'],
                    ]);

                    if (Schema::hasColumn('Products', 'stock') && $line['product_id']) {
                        $product = Product::query()->find($line['product_id']);

                        if ($product) {
                            $previousStock = (int) $product->stock;
                            $product->decrement('stock', $line['quantity']);
                            StockAlertNotifier::afterStockChange((int) $product->id, $previousStock);
                        }
                    }
                }

                if ($account) {
                    $account->increment('orders_synced_count');
                    $account->update(['last_sync_at' => now()]);
                }

                return $order->load('items');
            });

            $log = self::writeLog([
                'social_account_id' => $accountId,
                'platform' => $platform,
                'trigger_type' => $triggerType,
                'status' => 'success',
                'orders_imported' => 1,
                'external_order_id' => $externalOrderId,
                'order_id' => $order->id,
                'payload' => $data,
                'message' => 'Social order imported successfully.',
            ]);

            StoreNotifier::orderPlaced($order->fresh(['items.product']), strtoupper($platform));

            return [
                'status' => 'success',
                'order' => $order,
                'message' => 'Social order imported successfully.',
                'log' => $log,
            ];
        } catch (\Throwable $exception) {
            $log = self::writeLog([
                'social_account_id' => $accountId,
                'platform' => $platform,
                'trigger_type' => $triggerType,
                'status' => 'failed',
                'orders_imported' => 0,
                'external_order_id' => $externalOrderId,
                'payload' => $data,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public static function samplePayload(string $platform = 'instagram'): array
    {
        $platform = SocialPlatforms::normalizePlatform($platform);

        return [
            'platform' => $platform,
            'external_order_id' => strtoupper($platform).'-TEST-'.strtoupper(Str::random(6)),
            'external_account_id' => match ($platform) {
                'facebook' => 'fb_100000001',
                'instagram' => 'ig_100000001',
                'tiktok' => 'tt_100000001',
                'whatsapp' => 'wa_100000001',
                default => 'social_test_account',
            },
            'customer_name' => 'Social Test Customer',
            'customer_email' => 'social.test@example.com',
            'customer_phone' => '+92 300 0000000',
            'shipping' => 500,
            'notes' => 'Test order imported via social webhook structure.',
            'items' => [
                [
                    'product_name' => 'Social Platform Test Item',
                    'quantity' => 1,
                    'unit_price' => 2500,
                ],
            ],
            'source_metadata' => [
                'channel' => $platform,
                'imported_via' => 'test_webhook',
            ],
        ];
    }

    private static function ensureTables(): void
    {
        if (! Schema::hasTable('Orders') || ! Schema::hasTable('OrderItems')) {
            throw ValidationException::withMessages([
                'orders' => 'Orders tables are not available. Import cms/Orders.sql first.',
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private static function validatePayload(array $payload): array
    {
        $platform = SocialPlatforms::normalizePlatform((string) ($payload['platform'] ?? ''));

        if ($platform === 'website') {
            throw ValidationException::withMessages([
                'platform' => 'Use website checkout for MyBestStore website orders.',
            ]);
        }

        if (empty($payload['external_order_id'])) {
            throw ValidationException::withMessages([
                'external_order_id' => 'External order id is required for social orders.',
            ]);
        }

        if (empty($payload['customer_name']) || empty($payload['customer_email'])) {
            throw ValidationException::withMessages([
                'customer' => 'Customer name and email are required.',
            ]);
        }

        if (empty($payload['items']) || ! is_array($payload['items'])) {
            throw ValidationException::withMessages([
                'items' => 'At least one order item is required.',
            ]);
        }

        $payload['platform'] = $platform;
        $payload['external_order_id'] = (string) $payload['external_order_id'];

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function resolveAccount(array $data): ?SocialAccount
    {
        if (! Schema::hasTable('SocialAccounts')) {
            return null;
        }

        $platform = $data['platform'];
        $externalAccountId = $data['external_account_id'] ?? null;

        if ($externalAccountId) {
            $account = SocialAccount::query()
                ->where('platform', $platform)
                ->where('account_id', $externalAccountId)
                ->first();

            if ($account) {
                return $account;
            }
        }

        return SocialAccount::query()
            ->where('platform', $platform)
            ->where('status', 'connected')
            ->orderBy('id')
            ->first();
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    private static function buildLineItems(array $items): array
    {
        $lines = [];

        foreach ($items as $item) {
            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $productId = isset($item['product_id']) && $item['product_id'] !== '' && $item['product_id'] !== null
                ? (int) $item['product_id']
                : null;
            $productName = (string) ($item['product_name'] ?? 'Social Order Item');
            $unitPrice = (int) ($item['unit_price'] ?? 0);

            if ($productId && Schema::hasTable('Products')) {
                $product = Product::query()->find($productId);

                if ($product) {
                    $productName = $product->name;
                    if ($unitPrice <= 0) {
                        $unitPrice = (int) $product->price;
                    }
                }
            }

            $lines[] = [
                'product_id' => $productId,
                'product_name' => $productName,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $unitPrice * $quantity,
            ];
        }

        return $lines;
    }

    private static function generateOrderNumber(string $platform): string
    {
        $prefix = strtoupper(substr($platform, 0, 3));

        return 'MBS-'.$prefix.'-'.strtoupper(Str::random(8));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private static function writeLog(array $attributes): SocialSyncLog
    {
        if (! Schema::hasTable('SocialSyncLogs')) {
            return new SocialSyncLog($attributes);
        }

        return SocialSyncLog::create(array_merge($attributes, [
            'created_at' => now(),
        ]));
    }
}
