<?php

namespace App\Services\Payments;

use App\Models\Order;

class CodPaymentService implements PaymentGatewayInterface
{
    public function key(): string
    {
        return 'cash_on_delivery';
    }

    public function label(): string
    {
        return 'Cash on Delivery';
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function process(Order $order): array
    {
        return [
            'success' => true,
            'payment_status' => 'unpaid',
            'order_status' => 'confirmed',
            'message' => 'Your order has been placed. Pay on delivery.',
            'redirect_url' => null,
        ];
    }
}
