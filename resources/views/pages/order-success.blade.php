@extends('layouts.app')
@php
    use App\Support\Mbs;
    use App\Support\OrderPresenter;
@endphp

@section('title', 'MyBestStore | Order Success')

@section('content')
<section class="order-success-page">
    <div class="mbs-container order-success-shell">
        <div class="order-success-card">
            <div class="order-success-hero">
                <div class="order-success-icon" aria-hidden="true">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h1 class="order-success-title">Thank you for your order!</h1>
                <p class="order-success-subtitle">Your order has been placed successfully. We have sent the order details to your email.</p>
            </div>

            <div class="order-success-badge">
                <span class="order-success-badge-label">Order Number</span>
                <strong class="order-success-badge-value">{{ $order->order_number }}</strong>
            </div>

            <x-order-tracking-timeline :order="$order" class="order-success-timeline" />

            <div class="order-success-meta-grid">
                <div class="order-success-meta-item">
                    <span>Customer</span>
                    <strong>{{ $order->customer_name }}</strong>
                </div>
                <div class="order-success-meta-item">
                    <span>Payment Method</span>
                    <strong>{{ OrderPresenter::paymentLabel($order->payment_method) }}</strong>
                </div>
                <div class="order-success-meta-item">
                    <span>Payment Status</span>
                    <strong class="order-success-status order-success-status--payment">{{ OrderPresenter::paymentStatusLabel($order->payment_status) }}</strong>
                </div>
                <div class="order-success-meta-item">
                    <span>Order Status</span>
                    <strong class="order-success-status order-success-status--order">{{ OrderPresenter::statusLabel($order->order_status) }}</strong>
                </div>
                @if ($order->shipping_status)
                    <div class="order-success-meta-item">
                        <span>Shipping Status</span>
                        <strong>{{ OrderPresenter::shippingStatusLabel($order->shipping_status) }}</strong>
                    </div>
                @endif
                @if ($order->courier_name)
                    <div class="order-success-meta-item">
                        <span>Courier</span>
                        <strong>{{ $order->courier_name }}</strong>
                    </div>
                @endif
                @if ($order->tracking_number)
                    <div class="order-success-meta-item">
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
                    <div class="order-success-meta-item">
                        <span>Dispatched At</span>
                        <strong>{{ $order->dispatched_at->format('M j, Y g:i A') }}</strong>
                    </div>
                @endif
                <div class="order-success-meta-item order-success-meta-item--total">
                    <span>Total Amount</span>
                    <strong class="order-success-total">{{ Mbs::price((int) $order->total_amount) }}</strong>
                </div>
            </div>

            <div class="order-success-section">
                <h2>Order Summary</h2>
                <div class="order-success-items">
                    @foreach ($order->items as $item)
                        <div class="order-success-item">
                            <a href="{{ route('product.show', $item->product_slug) }}" class="order-success-item-image">
                                <img src="{{ Mbs::image($item->product_image) }}" alt="{{ $item->product_name }}">
                            </a>
                            <div class="order-success-item-body">
                                <a href="{{ route('product.show', $item->product_slug) }}" class="order-success-item-name">{{ $item->product_name }}</a>
                                <p class="order-success-item-meta">{{ $item->quantity }} × {{ Mbs::price((int) $item->unit_price) }}</p>
                            </div>
                            <strong class="order-success-item-total">{{ Mbs::price((int) $item->line_total) }}</strong>
                        </div>
                    @endforeach
                </div>

                <div class="order-success-totals">
                    <div><span>Subtotal</span><strong>{{ Mbs::price((int) $order->subtotal) }}</strong></div>
                    <div><span>Shipping</span><strong>{{ (int) $order->shipping_amount > 0 ? Mbs::price((int) $order->shipping_amount) : 'Free' }}</strong></div>
                    <div><span>Discount</span><strong>{{ (int) $order->discount_amount > 0 ? '-'.Mbs::price((int) $order->discount_amount) : '—' }}</strong></div>
                    <div class="order-success-totals-grand"><span>Grand Total</span><strong>{{ Mbs::price((int) $order->total_amount) }}</strong></div>
                </div>
            </div>

            <div class="order-success-section">
                <h2>Customer &amp; Shipping Details</h2>
                <div class="order-success-details-grid">
                    <div><span>Name</span><strong>{{ $order->customer_name }}</strong></div>
                    <div><span>Email</span><strong>{{ $order->customer_email }}</strong></div>
                    <div><span>Phone</span><strong>{{ $order->customer_phone }}</strong></div>
                    <div><span>Address</span><strong>{{ $order->shipping_address }}</strong></div>
                    <div><span>City</span><strong>{{ $order->city }}</strong></div>
                    <div><span>Province</span><strong>{{ $order->province }}</strong></div>
                    <div><span>Country</span><strong>{{ $order->country }}</strong></div>
                    @if ($order->postal_code)
                        <div><span>Postal Code</span><strong>{{ $order->postal_code }}</strong></div>
                    @endif
                </div>
            </div>

            @if ($order->payment_method === 'jazzcash')
                <div class="order-success-bank">
                    <h2>JazzCash Payment</h2>
                    <p class="order-success-bank-note">Please send payment to our JazzCash account after order confirmation. Your order will remain <strong>pending</strong> until payment is verified.</p>
                    @if ($order->payment_reference)
                        <p><strong>Reference:</strong> {{ $order->payment_reference }}</p>
                    @endif
                </div>
            @endif

            <div class="order-success-actions">
                <a href="{{ route('order.track', $order->order_number) }}" class="mbs-btn mbs-btn-outline">Track Order</a>
                <a href="{{ route('shop') }}" class="mbs-btn mbs-btn-primary">Continue Shopping</a>
                <a href="{{ route('home') }}" class="mbs-btn mbs-btn-outline">Back to Home</a>
                <a href="{{ route('order.invoice.download', $order->order_number) }}" class="mbs-btn mbs-btn-outline order-success-invoice-btn" target="_blank" rel="noopener">Download Invoice</a>
            </div>
        </div>
    </div>
</section>
@endsection
