@extends('cms::layouts.admin')

@section('title', 'Orders')

@section('page_heading')
<h1 class="sf-page-title">Orders</h1>
@endsection

@section('content')
<div class="sf-tabs">
    <a href="{{ route('cms.orders.index') }}" @class(['sf-tab', 'is-active' => $filter === 'all'])>All Orders</a>
    @foreach ($statuses as $status)
        <a href="{{ route('cms.orders.index', ['status' => $status]) }}" @class(['sf-tab', 'is-active' => $filter === $status])>{{ strtoupper($status) }}</a>
    @endforeach
</div>

<div class="sf-panel sf-panel--flush">
    <div class="sf-table-wrap">
        <table class="sf-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Source</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Method</th>
                    <th>Payment</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('cms.orders.show', $order->id) }}" class="sf-order-link">{{ $order->order_number }}</a>
                        </td>
                        <td>
                            <strong>{{ $order->customer_name }}</strong>
                            <span class="sf-subtext">{{ $order->customer_email }}</span>
                        </td>
                        <td><span class="sf-pill sf-pill--blue">{{ strtoupper($order->source ?? 'website') }}</span></td>
                        <td><strong>Rs {{ number_format($order->total) }}</strong></td>
                        <td>
                            @if ($canEdit)
                                <form method="POST" action="{{ route('cms.orders.quick-status', $order->id) }}" class="sf-inline-form">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="return_status" value="{{ $filter }}">
                                    <select name="status" class="sf-status-select" onchange="this.form.submit()">
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}" @selected($order->status === $status)>{{ strtoupper($status) }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            @else
                                <span class="sf-pill sf-pill--gray">{{ strtoupper($order->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="sf-pill sf-pill--blue">{{ strtoupper($order->payment_method ?? 'cod') }}</span>
                        </td>
                        <td>
                            @if ($canEdit)
                                <form method="POST" action="{{ route('cms.orders.quick-payment', $order->id) }}" class="sf-inline-form">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="return_status" value="{{ $filter }}">
                                    <select name="payment_status" class="sf-status-select" onchange="this.form.submit()">
                                        @foreach ($paymentStatuses as $paymentStatus)
                                            <option value="{{ $paymentStatus }}" @selected($order->payment_status === $paymentStatus)>{{ strtoupper($paymentStatus) }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            @else
                                <span class="sf-pill sf-pill--gray">{{ strtoupper($order->payment_status ?? 'pending') }}</span>
                            @endif
                        </td>
                        <td>{{ $order->created_at?->format('n/j/Y') }}</td>
                        <td><a href="{{ route('cms.orders.show', $order->id) }}">View</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="sf-empty">No orders found for this filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="sf-pagination">{{ $orders->links() }}</div>
@endsection
