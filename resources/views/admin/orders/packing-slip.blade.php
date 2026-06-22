<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packing Slip {{ $order->order_number }} | MyBestStore</title>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}?v={{ filemtime(public_path('assets/css/app.css')) }}">
</head>
<body class="packing-slip-page">
@php
    use App\Support\Mbs;
    use App\Support\OrderPresenter;
@endphp
<div class="packing-slip-sheet">
    <div class="packing-slip-toolbar no-print">
        <a href="{{ route('admin.orders.show', $order) }}" class="mbs-btn mbs-btn-outline">Back to Order</a>
        <button type="button" class="mbs-btn mbs-btn-primary" onclick="window.print()">Print Packing Slip</button>
    </div>

    <header class="packing-slip-header">
        <div>
            <p class="packing-slip-kicker">DISPATCH / PACKING SLIP</p>
            <h1 class="packing-slip-title">Order {{ $order->order_number }}</h1>
        </div>
        <x-order-barcode :order="$order" variant="large" class="packing-slip-barcode" />
    </header>

    <div class="packing-slip-grid">
        <section class="packing-slip-card">
            <h2>Customer</h2>
            <dl>
                <div><dt>Name</dt><dd>{{ $order->customer_name }}</dd></div>
                <div><dt>Phone</dt><dd>{{ $order->customer_phone }}</dd></div>
                <div><dt>City</dt><dd>{{ $order->city }}</dd></div>
                <div><dt>Address</dt><dd>{{ $order->shipping_address }}</dd></div>
                <div><dt>Full Address</dt><dd>{{ OrderPresenter::shippingAddress($order) }}</dd></div>
            </dl>
        </section>

        <section class="packing-slip-card">
            <h2>Order Status</h2>
            <dl>
                <div><dt>Payment Method</dt><dd>{{ OrderPresenter::paymentLabel($order->payment_method) }}</dd></div>
                <div><dt>Payment Status</dt><dd>{{ OrderPresenter::paymentStatusLabel($order->payment_status) }}</dd></div>
                <div><dt>Order Status</dt><dd>{{ OrderPresenter::statusLabel($order->order_status) }}</dd></div>
                <div><dt>Shipping Status</dt><dd>{{ OrderPresenter::shippingStatusLabel($order->shipping_status) }}</dd></div>
                <div><dt>Grand Total</dt><dd>{{ Mbs::price((int) $order->total_amount) }}</dd></div>
            </dl>
        </section>

        <section class="packing-slip-card">
            <h2>Dispatch</h2>
            <dl>
                <div><dt>Courier Company</dt><dd>{{ $order->courier_name ?: '—' }}</dd></div>
                <div><dt>Tracking Number</dt><dd>{{ $order->tracking_number ?: '—' }}</dd></div>
                <div><dt>Dispatched At</dt><dd>{{ $order->dispatched_at?->format('M j, Y g:i A') ?: '—' }}</dd></div>
                @if ($order->dispatched_by)
                    <div><dt>Dispatched By</dt><dd>{{ $order->dispatched_by }}</dd></div>
                @endif
            </dl>
        </section>
    </div>

    <div class="packing-slip-table-wrap">
        <table class="packing-slip-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Line Total</th>
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
    </div>
</div>
</body>
</html>
