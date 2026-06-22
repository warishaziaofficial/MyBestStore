@extends('layouts.admin')
@php use App\Support\Mbs; use App\Support\OrderPresenter; @endphp

@section('title', 'Order Dispatch')
@section('heading', 'Order Dispatch Scanner')

@section('content')
<div class="cms-card cms-dispatch-card">
    <form method="GET" action="{{ route('admin.orders.dispatch') }}" class="cms-dispatch-scan-form" id="dispatch_scan_form">
        <label for="dispatch_scan" class="cms-dispatch-label">Scan order barcode / QR or enter order number</label>
        <div class="cms-dispatch-scan-row">
            <input
                id="dispatch_scan"
                type="text"
                name="scan"
                value="{{ old('scan', $scan) }}"
                class="mbs-input cms-dispatch-input"
                placeholder="Scan barcode and press Enter"
                autocomplete="off"
                autofocus
            >
            <button type="submit" class="mbs-btn mbs-btn-primary">Search Order</button>
        </div>
        <small>Barcode scanners work like keyboard input. Focus stays on this field for fast scanning.</small>
    </form>
</div>

@if ($warning)
    <div class="cms-flash" style="background:#fff7ed;border:1px solid #fdba74;color:#9a3412;">{{ $warning }}</div>
@endif

@if ($order)
    <div class="cms-card">
        <div class="cms-order-meta">
            <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
            <p><strong>Order Barcode:</strong> {{ $order->order_barcode }}</p>
            <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
            <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
            <p><strong>City:</strong> {{ $order->city }}</p>
            <p><strong>Total:</strong> {{ Mbs::price((int) $order->total_amount) }}</p>
            <p><strong>Payment:</strong> {{ OrderPresenter::paymentLabel($order->payment_method) }}</p>
            <p><strong>Order Status:</strong> {{ OrderPresenter::statusLabel($order->order_status) }}</p>
            <p><strong>Shipping Status:</strong> {{ OrderPresenter::shippingStatusLabel($order->shipping_status) }}</p>
        </div>

        <x-order-barcode :order="$order" variant="large" class="cms-order-barcode cms-order-barcode--dispatch" />

        <table class="cms-table" style="margin-top:1rem;">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ Mbs::price((int) $item->line_total) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <form method="POST" action="{{ route('admin.orders.dispatch.store') }}" class="cms-dispatch-form">
            @csrf
            <input type="hidden" name="scan" value="{{ $order->order_barcode ?: $order->order_number }}">

            <div class="cms-field">
                <label for="courier_company_id">Courier company</label>
                <select id="courier_company_id" name="courier_company_id" class="mbs-input" required>
                    <option value="" disabled @selected(! old('courier_company_id', $order->courier_company_id))>Select courier</option>
                    @foreach ($couriers as $courier)
                        <option value="{{ $courier->id }}" @selected((int) old('courier_company_id', $order->courier_company_id) === $courier->id)>{{ $courier->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="cms-field">
                <label for="tracking_number">Tracking number</label>
                <input id="tracking_number" type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" class="mbs-input" required>
            </div>

            <div class="cms-field">
                <label for="dispatched_at">Dispatch date/time</label>
                <input id="dispatched_at" type="datetime-local" name="dispatched_at" value="{{ old('dispatched_at', optional($order->dispatched_at)->format('Y-m-d\TH:i') ?: now()->format('Y-m-d\TH:i')) }}" class="mbs-input">
            </div>

            <div class="cms-field">
                <label for="dispatched_by">Dispatched by</label>
                <input id="dispatched_by" type="text" name="dispatched_by" value="{{ old('dispatched_by', $order->dispatched_by ?: 'Admin') }}" class="mbs-input">
            </div>

            <div class="cms-field">
                <label for="dispatch_notes">Dispatch notes (optional)</label>
                <textarea id="dispatch_notes" name="dispatch_notes" class="mbs-input" rows="3">{{ old('dispatch_notes', $order->dispatch_notes) }}</textarea>
            </div>

            <div class="cms-actions">
                <button type="submit" class="mbs-btn mbs-btn-primary">
                    {{ $warning ? 'Update Dispatch Details' : 'Mark as Dispatched' }}
                </button>
                <a href="{{ route('admin.orders.show', $order) }}" class="cms-link">View order detail</a>
            </div>
        </form>
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('dispatch_scan');
        const form = document.getElementById('dispatch_scan_form');

        if (!input || !form) {
            return;
        }

        input.focus();
        input.select();

        input.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                form.submit();
            }
        });

        document.addEventListener('click', (event) => {
            if (!event.target.closest('.cms-dispatch-form') && !event.target.closest('a') && !event.target.closest('button[type="submit"]')) {
                setTimeout(() => input.focus(), 0);
            }
        });
    });
</script>
@endsection
