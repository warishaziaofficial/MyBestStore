@extends('layouts.admin')
@php use App\Support\OrderPresenter; @endphp

@section('title', 'Orders')
@section('heading', 'Orders')

@section('content')
<div class="cms-card">
    <div class="cms-actions" style="margin-top:0;margin-bottom:1rem;">
        <a href="{{ route('admin.orders.dispatch') }}" class="mbs-btn mbs-btn-primary">Dispatch Scanner</a>
    </div>

    <form method="GET" action="{{ route('admin.orders.index') }}" class="cms-search">
        <input type="search" name="q" value="{{ $query }}" class="mbs-input" placeholder="Search order number, barcode, tracking, phone...">
        <button type="submit" class="mbs-btn mbs-btn-primary">Search</button>
    </form>

    <table class="cms-table">
        <thead>
            <tr>
                <th>Order</th>
                <th>Customer</th>
                <th>Payment</th>
                <th>Order Status</th>
                <th>Shipping Status</th>
                <th>Courier</th>
                <th>Tracking</th>
                <th>Dispatched</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td>
                        <strong>{{ $order->order_number }}</strong>
                        @if ($order->order_barcode)
                            <br><small>{{ $order->order_barcode }}</small>
                        @endif
                    </td>
                    <td>
                        {{ $order->customer_name }}
                        <br><small>{{ $order->customer_phone }}</small>
                    </td>
                    <td>{{ OrderPresenter::paymentLabel($order->payment_method) }}</td>
                    <td>{{ OrderPresenter::statusLabel($order->order_status) }}</td>
                    <td>{{ OrderPresenter::shippingStatusLabel($order->shipping_status) }}</td>
                    <td>{{ $order->courier_name ?: '—' }}</td>
                    <td>{{ $order->tracking_number ?: '—' }}</td>
                    <td>{{ $order->dispatched_at?->format('M j, Y g:i A') ?: '—' }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}" class="cms-link">View</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if ($orders->hasPages())
        <div style="margin-top:1rem;">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
