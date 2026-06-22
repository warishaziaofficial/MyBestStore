<?php

namespace App\Services\Payments;

use App\Models\Order;

interface PaymentGatewayInterface
{
    public function key(): string;

    public function label(): string;

    public function isConfigured(): bool;

    /**
     * @return array{success: bool, payment_status: string, message: string, redirect_url?: string|null}
     */
    public function process(Order $order): array;
}
