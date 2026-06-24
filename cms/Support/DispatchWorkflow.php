<?php

namespace Cms\Support;

use App\Services\BarcodeService;
use Cms\Models\Order;
use Cms\Models\OrderItem;
use Cms\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class DispatchWorkflow
{
    public const STATUS_PENDING = 'pending_scan';

    public const STATUS_READY = 'ready';

    public const STATUS_DISPATCHED = 'dispatched';

    /** @return list<string> */
    public static function queueStatuses(): array
    {
        return [self::STATUS_PENDING, self::STATUS_READY, self::STATUS_DISPATCHED];
    }

    public static function productSku(?Product $product, ?int $productId = null): string
    {
        $id = $product?->id ?? $productId;

        if (! $id) {
            return 'N/A';
        }

        return 'MBS-'.str_pad((string) $id, 3, '0', STR_PAD_LEFT);
    }

    public static function productBarcode(?Product $product, ?int $productId = null): string
    {
        $id = $product?->id ?? $productId;

        if (! $id) {
            return '';
        }

        return '891234567890'.str_pad((string) $id, 2, '0', STR_PAD_LEFT);
    }

    /**
     * @return array<string, mixed>
     */
    public static function dispatchMeta(Order $order): array
    {
        if (Schema::hasColumn('Orders', 'dispatch_meta')) {
            $raw = $order->getAttributes()['dispatch_meta'] ?? null;
            $decoded = is_string($raw) ? json_decode($raw, true) : $raw;

            return is_array($decoded) ? $decoded : [];
        }

        $source = $order->source_metadata ?? [];

        return is_array($source['dispatch'] ?? null) ? $source['dispatch'] : [];
    }

    public static function dispatchStatus(Order $order): string
    {
        if (in_array($order->status, ['shipped', 'delivered'], true)) {
            return self::STATUS_DISPATCHED;
        }

        $meta = self::dispatchMeta($order);
        $stored = (string) ($meta['status'] ?? '');

        if (in_array($stored, self::queueStatuses(), true)) {
            return $stored;
        }

        return self::isFullyScanned($order) ? self::STATUS_READY : self::STATUS_PENDING;
    }

    public static function scannedQty(Order $order, OrderItem $item): int
    {
        $meta = self::dispatchMeta($order);
        $scans = is_array($meta['scans'] ?? null) ? $meta['scans'] : [];

        return min((int) $item->quantity, (int) ($scans[(string) $item->id] ?? 0));
    }

    public static function totalScannedUnits(Order $order): int
    {
        $order->loadMissing('items');

        return (int) $order->items->sum(fn (OrderItem $item) => self::scannedQty($order, $item));
    }

    public static function totalUnits(Order $order): int
    {
        $order->loadMissing('items');

        return (int) $order->items->sum('quantity');
    }

    public static function isFullyScanned(Order $order): bool
    {
        $total = self::totalUnits($order);

        return $total > 0 && self::totalScannedUnits($order) >= $total;
    }

    /**
     * @return array{scanned: int, total: int, percent: int, remaining: int}
     */
    public static function progress(Order $order): array
    {
        $total = self::totalUnits($order);
        $scanned = self::totalScannedUnits($order);
        $remaining = max(0, $total - $scanned);
        $percent = $total > 0 ? (int) round(($scanned / $total) * 100) : 0;

        return compact('scanned', 'total', 'percent', 'remaining');
    }

    public static function scanBarcode(Order $order, string $barcode): ?array
    {
        return self::scanCode($order, $barcode);
    }

    public static function scanCode(Order $order, string $code): ?array
    {
        $code = trim($code);

        if ($code === '') {
            return null;
        }

        $order->loadMissing(['items.product']);

        foreach ($order->items as $item) {
            $matches = ProductQr::matchCodes($item->product, $item->product_id);

            if (! ProductQr::scanMatches($code, $matches)) {
                continue;
            }

            $current = self::scannedQty($order, $item);

            if ($current >= (int) $item->quantity) {
                return [
                    'ok' => false,
                    'message' => $item->product_name.' is already fully scanned.',
                ];
            }

            self::setScannedQty($order, $item, $current + 1);

            return [
                'ok' => true,
                'message' => 'Scanned '.$item->product_name.' — '.($current + 1).'/'.$item->quantity,
                'product_name' => $item->product_name,
                'scanned' => $current + 1,
                'quantity' => (int) $item->quantity,
                'progress' => self::progress($order->fresh(['items.product'])),
                'ready' => self::isFullyScanned($order->fresh(['items.product'])),
            ];
        }

        $examples = $order->items
            ->map(fn (OrderItem $item) => ProductQr::code($item->product, $item->product_id))
            ->filter()
            ->take(3)
            ->implode(', ');

        return [
            'ok' => false,
            'message' => 'No matching product for "'.$code.'". Scan the QR or barcode shown in the table'
                .($examples !== '' ? ' (e.g. '.$examples.')' : '.'),
        ];
    }

    public static function productBarcodeSvg(?Product $product, ?int $productId = null, int $height = 36): string
    {
        $code = self::productBarcode($product, $productId);

        if ($code === '') {
            return '';
        }

        return app(BarcodeService::class)->barcodeSvg($code, $height, 1);
    }

    public static function scanItem(Order $order, OrderItem $item): array
    {
        $current = self::scannedQty($order, $item);

        if ($current >= (int) $item->quantity) {
            return [
                'ok' => false,
                'message' => $item->product_name.' is already fully scanned.',
            ];
        }

        self::setScannedQty($order, $item, $current + 1);
        $order = $order->fresh(['items.product']);

        return [
            'ok' => true,
            'message' => 'Scanned '.$item->product_name.' — '.($current + 1).'/'.$item->quantity,
            'product_name' => $item->product_name,
            'scanned' => $current + 1,
            'quantity' => (int) $item->quantity,
            'progress' => self::progress($order),
            'ready' => self::isFullyScanned($order),
        ];
    }

    public static function setScannedQty(Order $order, OrderItem $item, int $qty): void
    {
        $meta = self::dispatchMeta($order);
        $scans = is_array($meta['scans'] ?? null) ? $meta['scans'] : [];
        $scans[(string) $item->id] = max(0, min((int) $item->quantity, $qty));
        $meta['scans'] = $scans;
        $meta['status'] = self::isFullyScannedAfterScans($order, $scans)
            ? self::STATUS_READY
            : self::STATUS_PENDING;

        self::saveDispatchMeta($order, $meta);
    }

    /**
     * @param  array<string, int>  $scans
     */
    private static function isFullyScannedAfterScans(Order $order, array $scans): bool
    {
        $order->loadMissing('items');

        foreach ($order->items as $item) {
            if ((int) ($scans[(string) $item->id] ?? 0) < (int) $item->quantity) {
                return false;
            }
        }

        return $order->items->isNotEmpty();
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public static function saveDispatchMeta(Order $order, array $meta): void
    {
        if (Schema::hasColumn('Orders', 'dispatch_meta')) {
            $order->update(['dispatch_meta' => $meta]);

            return;
        }

        $source = $order->source_metadata ?? [];
        if (! is_array($source)) {
            $source = [];
        }

        $source['dispatch'] = $meta;
        $order->update(['source_metadata' => $source]);
    }

    public static function confirmDispatch(Order $order, string $courierKey, string $trackingNumber): Order
    {
        $courier = collect(config('couriers.couriers', []))->firstWhere('key', $courierKey);
        $meta = self::dispatchMeta($order);
        $meta['status'] = self::STATUS_DISPATCHED;
        $meta['courier_key'] = $courierKey;
        $meta['courier_name'] = $courier['name'] ?? ucfirst($courierKey);
        $meta['tracking_number'] = $trackingNumber;
        $meta['dispatched_at'] = now()->toIso8601String();

        self::saveDispatchMeta($order, $meta);

        $payload = ['status' => 'shipped'];

        if (Schema::hasColumn('Orders', 'courier_name')) {
            $payload['courier_name'] = $meta['courier_name'];
        }

        if (Schema::hasColumn('Orders', 'tracking_number')) {
            $payload['tracking_number'] = $trackingNumber;
        }

        if (Schema::hasColumn('Orders', 'dispatched_at')) {
            $payload['dispatched_at'] = now();
        }

        $order->update($payload);

        return $order->fresh(['items.product']);
    }

    public static function trackingNumber(Order $order): ?string
    {
        $meta = self::dispatchMeta($order);

        if (filled($meta['tracking_number'] ?? null)) {
            return (string) $meta['tracking_number'];
        }

        if (Schema::hasColumn('Orders', 'tracking_number')) {
            return $order->getAttributes()['tracking_number'] ?? null;
        }

        return null;
    }

    public static function courierName(Order $order): ?string
    {
        $meta = self::dispatchMeta($order);

        if (filled($meta['courier_name'] ?? null)) {
            return (string) $meta['courier_name'];
        }

        if (Schema::hasColumn('Orders', 'courier_name')) {
            return $order->getAttributes()['courier_name'] ?? null;
        }

        return null;
    }

    public static function dispatchedAt(Order $order): ?Carbon
    {
        $meta = self::dispatchMeta($order);

        if (filled($meta['dispatched_at'] ?? null)) {
            return Carbon::parse($meta['dispatched_at']);
        }

        if (Schema::hasColumn('Orders', 'dispatched_at') && filled($order->getAttributes()['dispatched_at'] ?? null)) {
            return Carbon::parse($order->getAttributes()['dispatched_at']);
        }

        return null;
    }

    public static function shippingAddressLine(Order $order): string
    {
        $parts = array_filter([
            $order->shipping_address,
            $order->city,
            $order->province,
            $order->postal_code,
            $order->country,
        ]);

        return $parts !== [] ? implode(', ', $parts) : 'Address not provided';
    }

    /**
     * @return Collection<int, Order>
     */
    public static function queueOrders(?string $filter = null): Collection
    {
        $orders = Order::query()
            ->with(['items.product'])
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered', 'pending'])
            ->whereNotIn('status', ['cancelled'])
            ->orderByDesc('id')
            ->get();

        return $orders
            ->map(function (Order $order) {
                $order->setAttribute('dispatch_status', self::dispatchStatus($order));
                $order->setAttribute('dispatch_progress', self::progress($order));

                return $order;
            })
            ->when($filter && $filter !== 'all', fn (Collection $c) => $c->filter(
                fn (Order $order) => $order->dispatch_status === $filter
            ))
            ->values();
    }

    /**
     * @return array{pending_scan: int, ready: int, dispatched_today: int}
     */
    public static function queueStats(Collection $orders): array
    {
        $today = now()->toDateString();

        return [
            'pending_scan' => $orders->where('dispatch_status', self::STATUS_PENDING)->count(),
            'ready' => $orders->where('dispatch_status', self::STATUS_READY)->count(),
            'dispatched_today' => $orders->filter(function (Order $order) use ($today) {
                if ($order->dispatch_status !== self::STATUS_DISPATCHED) {
                    return false;
                }

                $at = self::dispatchedAt($order);

                return $at && $at->toDateString() === $today;
            })->count(),
        ];
    }
}
