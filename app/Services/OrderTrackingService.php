<?php

namespace App\Services;

use App\Models\Order;
use App\Support\CmsIntegration;
use Cms\Models\Order as CmsOrder;

class OrderTrackingService
{
    public function findForCustomer(string $identifier, ?string $phone = null, ?string $email = null): Order|CmsOrder|null
    {
        $identifier = trim($identifier);
        $phone = trim((string) $phone);
        $email = strtolower(trim((string) $email));

        if ($identifier === '' || ($phone === '' && $email === '')) {
            return null;
        }

        if (CmsIntegration::preferCmsOrders()) {
            $order = CmsOrder::query()
                ->with(['items.product'])
                ->where('order_number', $identifier)
                ->first();
        } else {
            $order = Order::query()
                ->with(['items', 'courierCompany'])
                ->where(function ($query) use ($identifier) {
                    $query->where('order_number', $identifier)
                        ->orWhere('order_barcode', $identifier);
                })
                ->first();
        }

        if (! $order) {
            return null;
        }

        if (! $this->matchesCustomer($order, $phone, $email)) {
            return null;
        }

        return $order;
    }

    public function markVerified(Order|CmsOrder $order): void
    {
        session()->put($this->sessionKey($order), true);
    }

    public function isVerified(Order|CmsOrder $order): bool
    {
        return (bool) session($this->sessionKey($order), false);
    }

    private function matchesCustomer(Order|CmsOrder $order, string $phone, string $email): bool
    {
        $phoneMatch = false;
        $emailMatch = false;

        if ($phone !== '') {
            $normalizedInput = $this->normalizePhone($phone);
            $normalizedOrder = $this->normalizePhone((string) $order->customer_phone);
            $phoneMatch = $normalizedInput !== ''
                && ($normalizedOrder === $normalizedInput
                    || str_ends_with($normalizedOrder, $normalizedInput)
                    || str_ends_with($normalizedInput, $normalizedOrder));
        }

        if ($email !== '') {
            $emailMatch = strtolower((string) $order->customer_email) === $email;
        }

        return $phoneMatch || $emailMatch;
    }

    private function normalizePhone(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?? '';
    }

    private function sessionKey(Order|CmsOrder $order): string
    {
        return 'order_track_verified_'.$order->id;
    }
}
