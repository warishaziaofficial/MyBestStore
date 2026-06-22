<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourierCompany;
use App\Models\Order;
use App\Services\BarcodeService;
use App\Services\OrderDispatchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class OrderDispatchController extends Controller
{
    public function __construct(
        private readonly OrderDispatchService $dispatch,
        private readonly BarcodeService $barcodes,
    ) {}

    public function index(Request $request): View
    {
        $scan = trim($request->string('scan')->toString());
        $order = $scan !== '' ? $this->dispatch->findByScan($scan) : null;
        $warning = null;

        if ($scan !== '' && ! $order) {
            session()->flash('error', 'Order not found for this barcode.');
        }

        if ($order && $this->dispatch->isDispatched($order)) {
            $warning = 'This order is already dispatched. You can update courier and tracking details below.';
        }

        return view('admin.orders.dispatch', [
            'scan' => $scan,
            'order' => $order,
            'warning' => $warning,
            'couriers' => CourierCompany::query()->active()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'scan' => ['required', 'string', 'max:120'],
            'courier_company_id' => ['required', 'exists:courier_companies,id'],
            'tracking_number' => ['required', 'string', 'max:120'],
            'dispatched_at' => ['nullable', 'date'],
            'dispatch_notes' => ['nullable', 'string', 'max:1000'],
            'dispatched_by' => ['nullable', 'string', 'max:120'],
            'update_existing' => ['nullable', 'boolean'],
        ]);

        $order = $this->dispatch->findByScan($validated['scan']);

        if (! $order) {
            return redirect()
                ->route('admin.orders.dispatch')
                ->withInput()
                ->with('error', 'Order not found for this barcode.');
        }

        $payload = [
            'courier_company_id' => $validated['courier_company_id'],
            'tracking_number' => $validated['tracking_number'],
            'dispatched_at' => $validated['dispatched_at'] ?? null,
            'dispatch_notes' => $validated['dispatch_notes'] ?? null,
            'dispatched_by' => $validated['dispatched_by'] ?? 'Admin',
        ];

        if ($this->dispatch->isDispatched($order)) {
            $this->dispatch->updateDispatch($order, $payload);
        } else {
            $this->dispatch->dispatch($order, $payload);
        }

        return redirect()
            ->route('admin.orders.dispatch', ['scan' => $order->order_barcode ?: $order->order_number])
            ->with('success', 'Order marked as dispatched successfully.');
    }

    public function barcode(Order $order): Response
    {
        abort_unless(filled($order->order_barcode), 404);

        return response($this->barcodes->barcodeSvg($order->order_barcode), 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
