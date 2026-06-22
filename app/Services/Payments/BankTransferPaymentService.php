<?php

namespace App\Services\Payments;

class BankTransferPaymentService extends CodPaymentService
{
    public function key(): string
    {
        return 'bank_transfer';
    }

    public function label(): string
    {
        return 'Bank Transfer';
    }

    public function process(\App\Models\Order $order): array
    {
        return [
            'success' => true,
            'payment_status' => 'pending',
            'message' => 'Order saved. Please complete bank transfer using details on the confirmation page.',
            'redirect_url' => null,
        ];
    }
}
