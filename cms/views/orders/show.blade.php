@extends('cms::layouts.admin')

@section('title', 'Order '.$order->order_number)

@section('page_heading')
<div class="sf-page-head">
    <div>
        <h1 class="sf-page-title">Order {{ $order->order_number }}</h1>
        <p class="sf-page-subtitle">Placed {{ $order->created_at?->format('M j, Y g:i A') }}</p>
    </div>
    <div class="cms-inline-actions">
        <a href="{{ route('cms.resource.index', 'orders') }}" class="cms-muted">Back to orders</a>
        <a href="{{ route('cms.orders.invoice', $order->id) }}" class="cms-btn" target="_blank">Print Invoice</a>
        @if ($canEdit)
            <a href="{{ route('cms.resource.edit', ['orders', $order->id]) }}" class="cms-btn cms-btn-primary">Edit Order</a>
        @endif
    </div>
</div>
@endsection

@section('content')
<div class="cms-detail-grid">
    <div class="cms-panel">
        <h2>Customer</h2>
        <dl class="cms-dl">
            <dt>Name</dt><dd>{{ $order->customer_name }}</dd>
            <dt>Email</dt><dd>{{ $order->customer_email }}</dd>
            <dt>Phone</dt><dd>{{ $order->customer_phone ?: '—' }}</dd>
        </dl>
    </div>

    <div class="cms-panel">
        <h2>Status</h2>
        @if ($canEdit)
            <form method="POST" action="{{ route('cms.orders.update-status', $order->id) }}" class="cms-form cms-status-form">
                @csrf
                @method('PATCH')
                <label>
                    Order status
                    <select name="status">
                        @foreach ($orderStatuses as $status)
                            <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Payment status
                    <select name="payment_status">
                        @foreach ($paymentStatuses as $status)
                            <option value="{{ $status }}" @selected(($order->payment_status ?? 'pending') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </label>
                <button type="submit" class="cms-btn cms-btn-primary">Update Status</button>
            </form>
        @else
            <dl class="cms-dl">
                <dt>Order status</dt><dd><span class="cms-badge cms-badge--info">{{ $order->status }}</span></dd>
                <dt>Payment</dt><dd><span class="cms-badge cms-badge--info">{{ $order->payment_status ?? 'pending' }}</span></dd>
                <dt>Payment method</dt><dd>{{ strtoupper($order->payment_method ?? 'cod') }}</dd>
            </dl>
        @endif
        <dl class="cms-dl cms-dl--spaced">
            <dt>Source</dt><dd>{{ $order->source ?? 'website' }}</dd>
            @if ($order->external_order_id)
                <dt>External ID</dt><dd>{{ $order->external_order_id }}</dd>
            @endif
        </dl>
    </div>

    <div class="cms-panel">
        <h2>Totals</h2>
        <dl class="cms-dl">
            <dt>Subtotal</dt><dd>Rs {{ number_format($order->subtotal) }}</dd>
            <dt>Shipping</dt><dd>Rs {{ number_format($order->shipping) }}</dd>
            <dt>Total</dt><dd><strong>Rs {{ number_format($order->total) }}</strong></dd>
        </dl>
    </div>
</div>

@php
    use Cms\Support\DispatchWorkflow;
    $trackingNumber = DispatchWorkflow::trackingNumber($order);
    $courierName = DispatchWorkflow::courierName($order);
    $dispatchedAt = DispatchWorkflow::dispatchedAt($order);
@endphp
@if ($trackingNumber || $courierName)
    <div class="cms-panel">
        <h2>Dispatch &amp; Tracking</h2>
        <dl class="cms-dl">
            @if ($courierName)
                <dt>Courier</dt><dd>{{ $courierName }}</dd>
            @endif
            @if ($trackingNumber)
                <dt>Tracking #</dt><dd><code>{{ $trackingNumber }}</code></dd>
            @endif
            @if ($dispatchedAt)
                <dt>Dispatched</dt><dd>{{ $dispatchedAt->format('M j, Y g:i A') }}</dd>
            @endif
        </dl>
    </div>
@endif

<div class="cms-panel">
    <div class="cms-table-wrap">
        <table class="cms-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rs {{ number_format($item->unit_price) }}</td>
                        <td>Rs {{ number_format($item->line_total) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if ($order->notes)
    <div class="cms-panel">
        <h2>Notes</h2>
        <p>{{ $order->notes }}</p>
    </div>
@endif

<div class="cms-panel">
    <h2>Refunds</h2>
    @if ($order->refunds->isEmpty())
        <p class="cms-muted">No refunds recorded.</p>
    @else
        <div class="cms-table-wrap">
            <table class="cms-table">
                <thead>
                    <tr>
                        <th>Amount</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->refunds as $refund)
                        <tr>
                            <td>Rs {{ number_format($refund->amount) }}</td>
                            <td>{{ $refund->reason }}</td>
                            <td>{{ $refund->status }}</td>
                            <td>{{ $refund->created_at?->format('M j, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if ($canEdit)
        <form method="POST" action="{{ route('cms.orders.refund', $order->id) }}" class="cms-form cms-form-inline mt-4">
            @csrf
            <label>
                Refund amount (max Rs {{ number_format($order->total) }})
                <input type="number" name="amount" min="1" max="{{ $order->total }}" required>
            </label>
            <label>
                Reason
                <input type="text" name="reason" required placeholder="Customer return, damaged item, etc.">
            </label>
            <label>
                Notes
                <input type="text" name="notes" placeholder="Optional internal notes">
            </label>
            <button type="submit" class="cms-btn cms-btn-primary">Record Refund</button>
        </form>
    @endif
</div>
@endsection
