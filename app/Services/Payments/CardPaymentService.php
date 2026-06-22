<?php

namespace App\Services\Payments;

use App\Models\Order;

class CardPaymentService implements PaymentGatewayInterface
{
    public function key(): string
    {
        return 'card';
    }

    public function label(): string
    {
        return 'Card Payment';
    }

    public function isConfigured(): bool
    {
        return (bool) config('payments.card.public_key')
            && (bool) config('payments.card.secret_key');
    }

    public function process(Order $order): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => true,
                'payment_status' => 'pending',
                'message' => 'Order saved. Card payment integration coming soon.',
                'redirect_url' => null,
            ];
        }

        return [
            'success' => true,
            'payment_status' => 'pending',
            'message' => 'Redirecting to secure card checkout…',
            'redirect_url' => null,
        ];
    }
}
