<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Services\OrderTrackingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    public function __construct(
        private readonly OrderService $orders,
        private readonly OrderTrackingService $tracking,
    ) {}

    public function form(): View
    {
        return view('pages.track-order');
    }

    public function lookup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_number' => ['required', 'string', 'max:64'],
            'phone' => ['required_without:email', 'nullable', 'string', 'max:30'],
            'email' => ['required_without:phone', 'nullable', 'email', 'max:150'],
        ]);

        $order = $this->tracking->findForCustomer(
            $validated['order_number'],
            $validated['phone'] ?? null,
            $validated['email'] ?? null,
        );

        if (! $order) {
            return back()
                ->withInput()
                ->with('error', 'No order found for that order number and contact details.');
        }

        $this->tracking->markVerified($order);

        return redirect()->route('order.track', $order->order_number);
    }

    public function show(string $orderNumber): View|RedirectResponse
    {
        $order = $this->orders->findByNumber($orderNumber);

        abort_unless($order, 404);

        if (! $this->tracking->isVerified($order)) {
            return redirect()
                ->route('track-order')
                ->with('error', 'Please verify your order with order number and phone to view tracking.');
        }

        return view('pages.order-tracking', [
            'order' => $order,
        ]);
    }
}
