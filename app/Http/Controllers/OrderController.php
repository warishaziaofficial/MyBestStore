<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Services\OrderTrackingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orders,
        private readonly OrderTrackingService $tracking,
    ) {}

    public function success(string $orderNumber): View
    {
        $order = $this->orders->findByNumber($orderNumber);

        abort_unless($order, 404);

        $this->tracking->markVerified($order);

        return view('pages.order-success', [
            'order' => $order,
        ]);
    }

    public function invoice(string $orderNumber): View
    {
        $order = $this->orders->findByNumber($orderNumber);

        abort_unless($order, 404);

        return view('orders.invoice', [
            'order' => $order,
        ]);
    }

    /**
     * Invoice PDF download via dompdf.
     * Note: To embed logo/product images in PDFs, PHP GD extension must be installed/enabled.
     */
    public function downloadInvoice(string $orderNumber): RedirectResponse|Response
    {
        $order = $this->orders->findByNumber($orderNumber);

        abort_unless($order, 404);

        if (! extension_loaded('gd')) {
            return redirect()
                ->route('order.invoice', $order->order_number)
                ->with('error', 'PDF download requires PHP GD extension. Please enable GD extension or use Print Invoice.');
        }

        try {
            $pdf = Pdf::loadView('orders.invoice-pdf', ['order' => $order])
                ->setPaper('a4', 'portrait');

            return $pdf->download('invoice-'.$order->order_number.'.pdf');
        } catch (\Throwable $e) {
            Log::error('Invoice PDF download failed', [
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('order.invoice', $order->order_number)
                ->with('error', 'Invoice PDF could not be generated. Please try Print Invoice.');
        }
    }
}
