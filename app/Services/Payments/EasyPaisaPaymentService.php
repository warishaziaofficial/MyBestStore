<?php

namespace App\Services\Payments;

use App\Models\Order;

class EasyPaisaPaymentService implements PaymentGatewayInterface
{
    public function key(): string
    {
        return 'easypaisa';
    }

    public function label(): string
    {
        return 'EasyPaisa';
    }

    public function isConfigured(): bool
    {
        return (bool) config('payments.easypaisa.store_id')
            && (bool) config('payments.easypaisa.hash_key');
    }

    public function process(Order $order): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => true,
                'payment_status' => 'pending',
                'message' => 'Order saved. EasyPaisa payment integration coming soon.',
                'redirect_url' => null,
            ];
        }

        return [
            'success' => true,
            'payment_status' => 'pending',
            'message' => 'Redirecting to EasyPaisa…',
            'redirect_url' => null,
        ];
    }
}
