<?php

namespace Cms\Http\Controllers;

use Cms\Models\Order;
use Cms\Support\CmsAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrdersController extends Controller
{
    private const STATUSES = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];

    private const PAYMENT_STATUSES = ['pending', 'paid', 'failed', 'refunded'];

    public function index(Request $request): View
    {
        $filter = (string) $request->query('status', 'all');

        $query = Order::query()->orderByDesc('id');

        if ($filter !== 'all' && in_array($filter, self::STATUSES, true)) {
            $query->where('status', $filter);
        }

        return view('cms::orders.index', [
            'orders' => $query->paginate(15)->withQueryString(),
            'filter' => $filter,
            'statuses' => self::STATUSES,
            'paymentStatuses' => self::PAYMENT_STATUSES,
            'canEdit' => CmsAuth::canEdit(),
        ]);
    }

    public function quickStatus(Request $request, int $id): RedirectResponse
    {
        if (! CmsAuth::canEdit()) {
            abort(403);
        }

        $order = Order::query()->findOrFail($id);

        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', self::STATUSES)],
        ]);

        $order->update(['status' => $data['status']]);

        return redirect()
            ->route('cms.orders.index', ['status' => $request->query('return_status', 'all')])
            ->with('success', "Order {$order->order_number} updated to {$data['status']}.");
    }

    public function quickPaymentStatus(Request $request, int $id): RedirectResponse
    {
        if (! CmsAuth::canEdit()) {
            abort(403);
        }

        $order = Order::query()->findOrFail($id);

        $data = $request->validate([
            'payment_status' => ['required', 'in:'.implode(',', self::PAYMENT_STATUSES)],
        ]);

        $order->update(['payment_status' => $data['payment_status']]);

        return redirect()
            ->route('cms.orders.index', ['status' => $request->query('return_status', 'all')])
            ->with('success', "Payment status for {$order->order_number} updated to {$data['payment_status']}.");
    }
}
