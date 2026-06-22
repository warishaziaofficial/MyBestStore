@extends('layouts.app')
@php
    use App\Support\Mbs;
    use App\Support\OrderPresenter;
@endphp

@section('title', 'MyBestStore | Order Tracking')

@section('content')
<section class="order-tracking-page">
    <div class="mbs-container order-tracking-shell">
        <div class="order-tracking-card">
            <div class="order-tracking-head">
                <h1>Order Tracking</h1>
                <p>Order <strong>{{ $order->order_number }}</strong></p>
            </div>

            <x-order-tracking-timeline :order="$order" class="order-tracking-timeline-block" />

            <div class="order-tracking-meta">
                <div><span>Order Status</span><strong>{{ OrderPresenter::statusLabel($order->order_status) }}</strong></div>
                <div><span>Shipping Status</span><strong>{{ OrderPresenter::shippingStatusLabel($order->shipping_status) }}</strong></div>
                <div><span>Payment</span><strong>{{ OrderPresenter::paymentLabel($order->payment_method) }} · {{ OrderPresenter::paymentStatusLabel($order->payment_status) }}</strong></div>
                @if ($order->courier_name)
                    <div><span>Courier</span><strong>{{ $order->courier_name }}</strong></div>
                @endif
                @if ($order->tracking_number)
                    <div>
                        <span>Tracking Number</span>
                        <strong>
                            @if ($trackingUrl = OrderPresenter::trackingUrl($order))
                                <a href="{{ $trackingUrl }}" target="_blank" rel="noopener">{{ $order->tracking_number }}</a>
                            @else
                                {{ $order->tracking_number }}
                            @endif
                        </strong>
                    </div>
                @endif
                @if ($order->dispatched_at)
                    <div><span>Dispatched</span><strong>{{ $order->dispatched_at->format('M j, Y g:i A') }}</strong></div>
                @endif
                <div><span>Total</span><strong>{{ Mbs::price((int) $order->total_amount) }}</strong></div>
            </div>

            <div class="order-tracking-actions">
                <a href="{{ route('order.invoice', $order->order_number) }}" class="mbs-btn mbs-btn-outline" target="_blank" rel="noopener">View Invoice</a>
                <a href="{{ route('track-order') }}" class="mbs-btn mbs-btn-outline">Track Another Order</a>
                <a href="{{ route('shop') }}" class="mbs-btn mbs-btn-primary">Continue Shopping</a>
            </div>
        </div>
    </div>
</section>
@endsection
