<?php

namespace Cms\Http\Controllers;

use Cms\Models\Order;
use Cms\Models\Refund;
use Cms\Support\CmsAuth;
use Cms\Support\StoreNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderDetailController extends Controller
{
    public function show(int $id): View
    {
        $order = Order::query()->with(['items', 'refunds'])->findOrFail($id);

        return view('cms::orders.show', [
            'order' => $order,
            'canEdit' => CmsAuth::canEdit(),
            'orderStatuses' => ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'],
            'paymentStatuses' => ['pending', 'paid', 'failed', 'refunded'],
        ]);
    }

    public function invoice(int $id): View
    {
        $order = Order::query()->with('items')->findOrFail($id);

        return view('cms::orders.invoice', [
            'order' => $order,
        ]);
    }

    public function storeRefund(Request $request, int $id): RedirectResponse
    {
        $this->requireEditor();

        $order = Order::query()->findOrFail($id);

        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:1', 'max:'.$order->total],
            'reason' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        Refund::create([
            'order_id' => $order->id,
            'amount' => $data['amount'],
            'reason' => $data['reason'],
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
        ]);

        $order->update(['payment_status' => 'refunded']);

        return redirect()->route('cms.orders.show', $order->id)->with('success', 'Refund request recorded.');
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $this->requireEditor();

        $order = Order::query()->findOrFail($id);

        $data = $request->validate([
            'status' => ['nullable', 'in:pending,confirmed,processing,shipped,delivered,cancelled'],
            'payment_status' => ['nullable', 'in:pending,paid,failed,refunded'],
        ]);

        $updates = array_filter([
            'status' => $data['status'] ?? null,
            'payment_status' => $data['payment_status'] ?? null,
        ], fn ($value) => $value !== null);

        if ($updates === []) {
            return redirect()->route('cms.orders.show', $order->id)->with('success', 'No changes submitted.');
        }

        $previousStatus = (string) $order->status;

        $order->update($updates);
        $order->refresh();

        if (isset($updates['status'])) {
            StoreNotifier::orderStatusChanged($order, $previousStatus, (string) $order->status);
        }

        return redirect()->route('cms.orders.show', $order->id)->with('success', 'Order status updated.');
    }

    private function requireEditor(): void
    {
        if (! CmsAuth::canEdit()) {
            abort(403);
        }
    }
}
