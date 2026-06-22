<?php

namespace App\Services\Payments;

use App\Models\Order;

class JazzCashPaymentService implements PaymentGatewayInterface
{
    public function key(): string
    {
        return 'jazzcash';
    }

    public function label(): string
    {
        return 'JazzCash';
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function process(Order $order): array
    {
        return [
            'success' => true,
            'payment_status' => 'pending',
            'order_status' => 'pending',
            'message' => 'Order placed. JazzCash payment will be verified manually.',
            'redirect_url' => null,
        ];
    }
}
