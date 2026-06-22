<?php

namespace App\Services;

use App\Mail\OrderInvoiceMail;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Payments\PaymentManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
     * @return array{order: Order, message: string}
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

        $this->sendInvoiceEmail($result['order']);

        return $result;
    }

    private function sendInvoiceEmail(Order $order): void
    {
        try {
            Mail::to($order->customer_email)->send(new OrderInvoiceMail($order));
        } catch (\Throwable $e) {
            Log::error('Order invoice email failed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function findByNumber(string $orderNumber): ?Order
    {
        return Order::query()->with(['items', 'courierCompany'])->where('order_number', $orderNumber)->first();
    }

    public function findByScan(string $scan): ?Order
    {
        $scan = trim($scan);

        if ($scan === '') {
            return null;
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
        } while (Order::query()->where('order_number', $number)->exists());

        return $number;
    }
}
