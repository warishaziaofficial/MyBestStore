<?php

namespace Cms\Http\Controllers\Api;

use Cms\Http\Controllers\Controller;
use Cms\Models\Order;
use Cms\Support\AdminApiFormatter;
use Cms\Support\CmsAuth;
use Cms\Support\StoreNotifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class OrderApiController extends Controller
{
    private const STATUSES = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];

    private const PAYMENT_STATUSES = ['pending', 'paid', 'failed', 'refunded'];

    private const SOURCES = ['website', 'facebook', 'instagram', 'tiktok', 'whatsapp', 'other'];

    public function index(Request $request): JsonResponse
    {
        if (! Schema::hasTable('Orders')) {
            return response()->json(['data' => [], 'meta' => ['total' => 0]]);
        }

        $query = Order::query()->orderByDesc('id');

        if ($status = $request->query('status')) {
            $query->where('status', strtolower((string) $status));
        }

        if ($request->filled('source') && Schema::hasColumn('Orders', 'source')) {
            $query->where('source', strtolower((string) $request->query('source')));
        }

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('order_number', 'like', '%'.$search.'%')
                    ->orWhere('customer_name', 'like', '%'.$search.'%')
                    ->orWhere('customer_email', 'like', '%'.$search.'%');
            });
        }

        $paginator = $query->paginate((int) $request->query('per_page', 20));

        return response()->json([
            'data' => collect($paginator->items())
                ->map(fn (Order $order) => AdminApiFormatter::order($order))
                ->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'statuses' => self::STATUSES,
                'sources' => self::SOURCES,
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $order = Order::query()->with('items')->findOrFail($id);

        return response()->json([
            'data' => AdminApiFormatter::order($order, true),
        ]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $this->requireEditor();

        $data = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
        ]);

        $order = Order::query()->findOrFail($id);
        $previousStatus = (string) $order->status;
        $order->update(['status' => $data['status']]);
        $order->refresh();

        StoreNotifier::orderStatusChanged($order, $previousStatus, (string) $order->status);

        return response()->json([
            'message' => 'Order status updated.',
            'data' => AdminApiFormatter::order($order->fresh()),
        ]);
    }

    public function updatePaymentStatus(Request $request, int $id): JsonResponse
    {
        $this->requireEditor();

        $data = $request->validate([
            'payment_status' => ['required', Rule::in(self::PAYMENT_STATUSES)],
        ]);

        $order = Order::query()->findOrFail($id);
        $order->update(['payment_status' => $data['payment_status']]);

        return response()->json([
            'message' => 'Payment status updated.',
            'data' => AdminApiFormatter::order($order->fresh()),
        ]);
    }

    public function updateSource(Request $request, int $id): JsonResponse
    {
        $this->requireEditor();

        if (! Schema::hasColumn('Orders', 'source')) {
            return response()->json(['message' => 'Order source is not enabled. Import cms/Orders-social-fields.sql.'], 503);
        }

        $data = $request->validate([
            'source' => ['required', Rule::in(self::SOURCES)],
        ]);

        $order = Order::query()->findOrFail($id);
        $order->update(['source' => $data['source']]);

        return response()->json([
            'message' => 'Order source updated.',
            'data' => AdminApiFormatter::order($order->fresh()),
        ]);
    }

    private function requireEditor(): void
    {
        if (! CmsAuth::canEdit()) {
            abort(403, 'You do not have permission to perform this action.');
        }
    }
}
