<?php

namespace App\Services;

use App\Models\CourierCompany;
use App\Models\Order;
use Illuminate\Support\Carbon;

class OrderDispatchService
{
    public function findByScan(string $scan): ?Order
    {
        $scan = trim($scan);

        if ($scan === '') {
            return null;
        }

        return Order::query()
            ->with(['items', 'courierCompany'])
            ->where(function ($query) use ($scan) {
                $query->where('order_barcode', $scan)
                    ->orWhere('order_number', $scan)
                    ->orWhere('tracking_number', $scan);
            })
            ->first();
    }

    public function isDispatched(Order $order): bool
    {
        return in_array($order->shipping_status, ['dispatched', 'in_transit', 'delivered'], true)
            || in_array($order->order_status, ['dispatched', 'shipped', 'delivered'], true);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function dispatch(Order $order, array $data): Order
    {
        $courier = CourierCompany::query()->findOrFail($data['courier_company_id']);

        $dispatchedAt = filled($data['dispatched_at'] ?? null)
            ? Carbon::parse($data['dispatched_at'])
            : now();

        $order->update([
            'shipping_status' => 'dispatched',
            'order_status' => 'shipped',
            'courier_company_id' => $courier->id,
            'courier_name' => $courier->name,
            'tracking_number' => $data['tracking_number'],
            'dispatched_at' => $dispatchedAt,
            'dispatched_by' => $data['dispatched_by'] ?? 'Admin',
            'dispatch_notes' => $data['dispatch_notes'] ?? null,
        ]);

        return $order->fresh(['items', 'courierCompany']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateDispatch(Order $order, array $data): Order
    {
        $courier = CourierCompany::query()->findOrFail($data['courier_company_id']);

        $order->update([
            'courier_company_id' => $courier->id,
            'courier_name' => $courier->name,
            'tracking_number' => $data['tracking_number'],
            'dispatched_at' => filled($data['dispatched_at'] ?? null) ? Carbon::parse($data['dispatched_at']) : $order->dispatched_at,
            'dispatched_by' => $data['dispatched_by'] ?? $order->dispatched_by,
            'dispatch_notes' => $data['dispatch_notes'] ?? $order->dispatch_notes,
            'shipping_status' => $data['shipping_status'] ?? $order->shipping_status,
            'order_status' => $data['order_status'] ?? $order->order_status,
        ]);

        return $order->fresh(['items', 'courierCompany']);
    }
}
