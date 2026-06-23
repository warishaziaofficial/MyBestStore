<?php

namespace Cms\Http\Controllers;

use Cms\Models\Order;
use Cms\Models\OrderItem;
use Cms\Support\DispatchWorkflow;
use Cms\Support\StoreNotifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DispatchController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'all');
        $orders = DispatchWorkflow::queueOrders($filter === 'all' ? null : $filter);
        $allOrders = DispatchWorkflow::queueOrders();

        return view('cms::dispatch.queue', [
            'orders' => $orders,
            'filter' => $filter,
            'stats' => DispatchWorkflow::queueStats($allOrders),
            'couriers' => config('couriers.couriers', []),
        ]);
    }

    public function scan(int $id): View|RedirectResponse
    {
        $order = $this->findOrder($id);

        if (DispatchWorkflow::dispatchStatus($order) === DispatchWorkflow::STATUS_DISPATCHED) {
            return redirect()
                ->route('cms.dispatch.queue')
                ->with('error', 'This order has already been dispatched.');
        }

        return view('cms::dispatch.scan', [
            'order' => $order,
            'progress' => DispatchWorkflow::progress($order),
            'status' => DispatchWorkflow::dispatchStatus($order),
        ]);
    }

    public function scanBarcode(Request $request, int $id): JsonResponse
    {
        $order = $this->findOrder($id);
        $code = trim((string) ($request->input('code', $request->input('barcode', ''))));

        $result = DispatchWorkflow::scanCode($order, $code);

        if (! $result) {
            return response()->json([
                'ok' => false,
                'message' => 'Please scan or enter a QR code.',
            ], 422);
        }

        return response()->json($result, $result['ok'] ? 200 : 422);
    }

    public function scanItem(int $id, int $itemId): JsonResponse
    {
        $order = $this->findOrder($id);
        $item = $order->items->firstWhere('id', $itemId);

        abort_unless($item instanceof OrderItem, 404);

        $result = DispatchWorkflow::scanItem($order, $item);

        return response()->json($result, $result['ok'] ? 200 : 422);
    }

    public function ship(int $id): View|RedirectResponse
    {
        $order = $this->findOrder($id);

        if (DispatchWorkflow::dispatchStatus($order) === DispatchWorkflow::STATUS_DISPATCHED) {
            return redirect()->route('cms.dispatch.queue');
        }

        if (! DispatchWorkflow::isFullyScanned($order)) {
            return redirect()
                ->route('cms.dispatch.scan', $order->id)
                ->with('error', 'Scan all items before dispatch.');
        }

        return view('cms::dispatch.ship', [
            'order' => $order,
            'couriers' => config('couriers.couriers', []),
            'progress' => DispatchWorkflow::progress($order),
        ]);
    }

    public function confirmShip(Request $request, int $id): RedirectResponse
    {
        $order = $this->findOrder($id);

        if (! DispatchWorkflow::isFullyScanned($order)) {
            return redirect()
                ->route('cms.dispatch.scan', $order->id)
                ->with('error', 'Scan all items before dispatch.');
        }

        $validated = $request->validate([
            'courier_key' => ['required', 'string', 'max:50'],
            'tracking_number' => ['required', 'string', 'max:120'],
        ]);

        $courierKeys = collect(config('couriers.couriers', []))->pluck('key')->all();

        if (! in_array($validated['courier_key'], $courierKeys, true)) {
            return back()->withInput()->with('error', 'Please select a valid courier.');
        }

        DispatchWorkflow::confirmDispatch($order, $validated['courier_key'], $validated['tracking_number']);

        StoreNotifier::orderDispatched($order->fresh(['items.product']));

        return redirect()
            ->route('cms.dispatch.queue', ['filter' => 'dispatched'])
            ->with('success', 'Order '.$order->order_number.' dispatched successfully.');
    }

    private function findOrder(int $id): Order
    {
        return Order::query()->with(['items.product'])->findOrFail($id);
    }
}
