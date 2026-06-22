@extends('layouts.admin')
@php
    use App\Support\Mbs;
    use App\Support\OrderPresenter;
@endphp

@section('title', 'Order '.$order->order_number)
@section('heading', 'Order Details')

@section('content')
<div class="cms-card">
    <div class="cms-actions cms-order-actions" style="margin-top:0;margin-bottom:1rem;">
        <a href="{{ route('admin.orders.index') }}" class="cms-link">Back to Orders</a>
        <a href="{{ route('order.invoice', $order->order_number) }}" class="mbs-btn mbs-btn-outline" target="_blank" rel="noopener">View Invoice</a>
        <a href="{{ route('order.invoice', $order->order_number) }}?print=1" class="mbs-btn mbs-btn-outline" target="_blank" rel="noopener">Print Invoice</a>
        <a href="{{ route('admin.orders.packing-slip', $order) }}" class="mbs-btn mbs-btn-outline" target="_blank" rel="noopener">Print Packing Slip</a>
        <a href="{{ route('admin.orders.dispatch', ['scan' => $order->order_barcode ?: $order->order_number]) }}" class="mbs-btn mbs-btn-primary">Dispatch Order</a>
    </div>

    @if ($order->order_barcode)
        <x-order-barcode :order="$order" variant="compact" class="cms-order-barcode" />
    @endif

    <div class="cms-order-meta">
        <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
        <p><strong>Order Barcode:</strong> {{ $order->order_barcode ?: '—' }}</p>
        <p><strong>Customer:</strong> {{ $order->customer_name }} · {{ $order->customer_phone }} · {{ $order->customer_email }}</p>
        <p><strong>City:</strong> {{ $order->city }}, {{ $order->province }}, {{ $order->country }}</p>
        <p><strong>Payment:</strong> {{ OrderPresenter::paymentLabel($order->payment_method) }} · {{ OrderPresenter::paymentStatusLabel($order->payment_status) }}</p>
        <p><strong>Order Status:</strong> {{ OrderPresenter::statusLabel($order->order_status) }}</p>
        <p><strong>Shipping Status:</strong> {{ OrderPresenter::shippingStatusLabel($order->shipping_status) }}</p>
        <p><strong>Total:</strong> {{ Mbs::price((int) $order->total_amount) }}</p>
        @if ($order->courier_name)
            <p><strong>Courier:</strong> {{ $order->courier_name }}</p>
        @endif
        @if ($order->tracking_number)
            <p>
                <strong>Tracking:</strong> {{ $order->tracking_number }}
                @if ($trackingUrl = OrderPresenter::trackingUrl($order))
                    · <a href="{{ $trackingUrl }}" class="cms-link" target="_blank" rel="noopener">Track shipment</a>
                @endif
            </p>
        @endif
        @if ($order->dispatched_at)
            <p><strong>Dispatched:</strong> {{ $order->dispatched_at->format('M j, Y g:i A') }} @if($order->dispatched_by) by {{ $order->dispatched_by }} @endif</p>
        @endif
        @if ($order->dispatch_notes)
            <p><strong>Dispatch Notes:</strong> {{ $order->dispatch_notes }}</p>
        @endif
    </div>

    <table class="cms-table" style="margin-top:1.5rem;">
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ Mbs::price((int) $item->unit_price) }}</td>
                    <td>{{ Mbs::price((int) $item->line_total) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
