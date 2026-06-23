<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Payments\PaymentGatewayInterface;
use App\Services\Payments\PaymentManager;
use App\Support\CmsIntegration;
use Cms\Models\Order as CmsOrder;
use Cms\Models\OrderItem as CmsOrderItem;
use Cms\Models\Product as CmsProduct;
use Cms\Support\StockAlertNotifier;
use Cms\Support\StoreNotifier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private readonly CartService $cart,
        private readonly PaymentManager $payments,
        private readonly BarcodeService $barcodes,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array{order: Order|CmsOrder, message: string, redirect_url?: string|null}
     */
    public function place(array $data): array
    {
        if ($this->cart->isEmpty()) {
            throw new \RuntimeException('Your cart is empty.');
        }

        $gateway = $this->payments->get($data['payment_method'] ?? 'cash_on_delivery');

        if (! $gateway) {
            throw new \RuntimeException('Invalid payment method selected.');
        }

        if (CmsIntegration::preferCmsOrders()) {
            return $this->placeCmsOrder($data, $gateway);
        }

        $result = DB::transaction(function () use ($data, $gateway) {
            $customer = Customer::query()->firstOrCreate(
                ['email' => $data['customer_email']],
                [
                    'name' => $data['customer_name'],
                    'phone' => $data['customer_phone'],
                    'password' => null,
                ]
            );

            $customer->update([
                'name' => $data['customer_name'],
                'phone' => $data['customer_phone'],
            ]);

            $shippingAmount = (int) ($data['shipping_amount'] ?? 0);
            $totalAmount = $this->cart->subtotal() + $shippingAmount - $this->cart->discount();

            $order = Order::query()->create([
                'customer_id' => $customer->id,
                'order_number' => $this->generateOrderNumber(),
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'shipping_address' => $data['shipping_address'],
                'city' => $data['city'],
                'province' => $data['province'],
                'country' => $data['country'],
                'postal_code' => $data['postal_code'] ?? null,
                'subtotal' => $this->cart->subtotal(),
                'shipping_amount' => $shippingAmount,
                'shipping_method' => $data['shipping_method'] ?? null,
                'shipping_zone' => $data['shipping_zone'] ?? null,
                'total_weight' => $data['total_weight'] ?? $this->cart->totalWeight(),
                'discount_amount' => $this->cart->discount(),
                'total_amount' => max(0, $totalAmount),
                'payment_method' => $gateway->key(),
                'payment_status' => 'unpaid',
                'payment_reference' => $data['payment_reference'] ?? null,
                'payment_notes' => $data['payment_notes'] ?? null,
                'order_status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($this->cart->items() as $item) {
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_slug' => $item['slug'],
                    'product_name' => $item['name'],
                    'product_image' => $item['image'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'line_total' => $item['price'] * $item['quantity'],
                ]);
            }

            $paymentResult = $gateway->process($order);

            $order->update([
                'payment_status' => $paymentResult['payment_status'],
                'order_status' => $paymentResult['order_status'] ?? ($paymentResult['success'] ? 'confirmed' : 'pending'),
                'order_barcode' => $this->barcodes->makeOrderBarcode((int) $order->id),
                'shipping_status' => 'pending',
            ]);

            $this->cart->clear();

            return [
                'order' => $order->fresh('items'),
                'message' => $paymentResult['message'],
                'redirect_url' => $paymentResult['redirect_url'] ?? null,
            ];
        });

        StoreNotifier::orderPlaced($result['order'], $gateway->label());

        return $result;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{order: CmsOrder, message: string, redirect_url?: string|null}
     */
    private function placeCmsOrder(array $data, PaymentGatewayInterface $gateway): array
    {
        if (Schema::hasColumn('Products', 'stock')) {
            foreach ($this->cart->items() as $item) {
                $product = CmsProduct::query()->where('slug', $item['slug'])->first();

                if ($product && (int) $product->stock < (int) $item['quantity']) {
                    throw new \RuntimeException('Insufficient stock for '.$product->name.'.');
                }
            }
        }

        $shippingAmount = (int) ($data['shipping_amount'] ?? 0);
        $subtotal = $this->cart->subtotal();
        $totalAmount = max(0, $subtotal + $shippingAmount - $this->cart->discount());
        $paymentMethod = CmsIntegration::cmsPaymentMethod($gateway->key());

        $result = DB::transaction(function () use ($data, $gateway, $shippingAmount, $subtotal, $totalAmount, $paymentMethod) {
            $notes = json_encode([
                'shipping' => [
                    'address' => $data['shipping_address'] ?? null,
                    'city' => $data['city'] ?? null,
                    'province' => $data['province'] ?? null,
                    'country' => $data['country'] ?? null,
                    'postal_code' => $data['postal_code'] ?? null,
                    'method' => $data['shipping_method'] ?? null,
                    'zone' => $data['shipping_zone'] ?? null,
                ],
                'customer_notes' => $data['notes'] ?? null,
            ], JSON_UNESCAPED_UNICODE);

            $payload = [
                'order_number' => $this->generateOrderNumber(),
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'status' => 'pending',
                'payment_status' => 'pending',
                'subtotal' => $subtotal,
                'shipping' => $shippingAmount,
                'total' => $totalAmount,
                'notes' => $notes,
            ];

            if (Schema::hasColumn('Orders', 'source')) {
                $payload['source'] = 'website';
            }

            if (Schema::hasColumn('Orders', 'payment_method')) {
                $payload['payment_method'] = $paymentMethod;
            }

            $order = CmsOrder::query()->create($payload);

            foreach ($this->cart->items() as $item) {
                $product = CmsProduct::query()->where('slug', $item['slug'])->first();

                CmsOrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $product?->id,
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'line_total' => $item['price'] * $item['quantity'],
                ]);

                if ($product && Schema::hasColumn('Products', 'stock')) {
                    $previousStock = (int) $product->stock;
                    $product->decrement('stock', (int) $item['quantity']);
                    StockAlertNotifier::afterStockChange((int) $product->id, $previousStock);
                }
            }

            $paymentResult = $this->resolveCmsPaymentResult($gateway, $paymentMethod);

            $order->update([
                'payment_status' => $this->mapCmsPaymentStatus($paymentResult['payment_status']),
                'status' => $paymentResult['order_status'] ?? ($paymentResult['success'] ? 'confirmed' : 'pending'),
            ]);

            $this->cart->clear();

            return [
                'order' => $order->fresh(['items.product']),
                'message' => $paymentResult['message'],
                'redirect_url' => $paymentResult['redirect_url'] ?? null,
            ];
        });

        StoreNotifier::orderPlaced($result['order'], $gateway->label());

        return $result;
    }

    /**
     * @return array{success: bool, payment_status: string, order_status?: string, message: string, redirect_url?: string|null}
     */
    private function resolveCmsPaymentResult(PaymentGatewayInterface $gateway, string $paymentMethod): array
    {
        return match ($paymentMethod) {
            'cod', 'cash_on_delivery' => [
                'success' => true,
                'payment_status' => 'unpaid',
                'order_status' => 'confirmed',
                'message' => 'Your order has been placed. Pay on delivery.',
                'redirect_url' => null,
            ],
            default => [
                'success' => true,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'message' => 'Your order has been placed.',
                'redirect_url' => null,
            ],
        };
    }

    private function mapCmsPaymentStatus(string $status): string
    {
        return match ($status) {
            'unpaid' => 'pending',
            default => $status,
        };
    }

    public function findByNumber(string $orderNumber): Order|CmsOrder|null
    {
        if (CmsIntegration::preferCmsOrders()) {
            return CmsOrder::query()
                ->with(['items.product'])
                ->where('order_number', $orderNumber)
                ->first();
        }

        return Order::query()->with(['items', 'courierCompany'])->where('order_number', $orderNumber)->first();
    }

    public function findByScan(string $scan): Order|CmsOrder|null
    {
        $scan = trim($scan);

        if ($scan === '') {
            return null;
        }

        if (CmsIntegration::preferCmsOrders()) {
            return CmsOrder::query()
                ->with(['items.product'])
                ->where('order_number', $scan)
                ->first();
        }

        return Order::query()
            ->with(['items', 'courierCompany'])
            ->where(function ($query) use ($scan) {
                $query->where('order_number', $scan)
                    ->orWhere('order_barcode', $scan)
                    ->orWhere('tracking_number', $scan);
            })
            ->first();
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'MBS-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (
            (CmsIntegration::preferCmsOrders() && CmsOrder::query()->where('order_number', $number)->exists())
            || (! CmsIntegration::preferCmsOrders() && Order::query()->where('order_number', $number)->exists())
        );

        return $number;
    }
}
