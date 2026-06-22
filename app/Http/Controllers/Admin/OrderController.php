<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim($request->string('q')->toString());

        $orders = Order::query()
            ->with('courierCompany')
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('order_number', 'like', "%{$query}%")
                        ->orWhere('order_barcode', 'like', "%{$query}%")
                        ->orWhere('tracking_number', 'like', "%{$query}%")
                        ->orWhere('customer_phone', 'like', "%{$query}%")
                        ->orWhere('customer_name', 'like', "%{$query}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'query' => $query,
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['items', 'courierCompany']);

        return view('admin.orders.show', [
            'order' => $order,
        ]);
    }

    public function packingSlip(Order $order): View
    {
        $order->load(['items', 'courierCompany']);

        return view('admin.orders.packing-slip', [
            'order' => $order,
        ]);
    }
}
