@extends('cms::layouts.admin')

@section('title', 'Dispatch Queue')

@section('page_heading')
    <div class="dp-page-head">
        <div>
            <h1 class="sf-page-title">Dispatch Queue</h1>
            <p class="sf-page-sub">Scan items, verify orders, and hand off to couriers.</p>
        </div>
        <span class="dp-date">{{ now()->format('M j, Y') }}</span>
    </div>
@endsection

@section('content')
@php
    use Cms\Support\DispatchWorkflow;
@endphp

<div class="dp-stats">
    <div class="dp-stat">
        <span class="dp-stat-value dp-stat-value--orange">{{ $stats['pending_scan'] }}</span>
        <span class="dp-stat-label">Pending Scan</span>
    </div>
    <div class="dp-stat">
        <span class="dp-stat-value dp-stat-value--blue">{{ $stats['ready'] }}</span>
        <span class="dp-stat-label">Ready to Dispatch</span>
    </div>
    <div class="dp-stat">
        <span class="dp-stat-value dp-stat-value--green">{{ $stats['dispatched_today'] }}</span>
        <span class="dp-stat-label">Dispatched Today</span>
    </div>
</div>

<div class="dp-filters">
    @foreach (['all' => 'All Orders', DispatchWorkflow::STATUS_PENDING => 'Pending', DispatchWorkflow::STATUS_READY => 'Ready', DispatchWorkflow::STATUS_DISPATCHED => 'Dispatched'] as $key => $label)
        <a href="{{ route('cms.dispatch.queue', ['filter' => $key]) }}" @class(['dp-filter-pill', 'is-active' => $filter === $key])>{{ $label }}</a>
    @endforeach
</div>

<div class="sf-card dp-queue-card">
    <table class="sf-table dp-queue-table">
        <thead>
            <tr>
                <th>Order</th>
                <th>Customer</th>
                <th>Progress</th>
                <th>Order Date</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                @php
                    $status = $order->dispatch_status;
                    $progress = $order->dispatch_progress;
                    $rowClass = match ($status) {
                        DispatchWorkflow::STATUS_DISPATCHED => 'dp-row--dispatched',
                        DispatchWorkflow::STATUS_READY => 'dp-row--ready',
                        default => '',
                    };
                @endphp
                <tr class="{{ $rowClass }}">
                    <td><strong>{{ $order->order_number }}</strong></td>
                    <td>
                        <strong>{{ $order->customer_name }}</strong>
                        <span class="dp-muted">{{ DispatchWorkflow::shippingAddressLine($order) }}</span>
                    </td>
                    <td>{{ $progress['scanned'] }}/{{ $progress['total'] }} items</td>
                    <td>{{ $order->created_at?->format('M j, Y') }}</td>
                    <td>
                        @if ($status === DispatchWorkflow::STATUS_DISPATCHED)
                            <span class="dp-status dp-status--green">Dispatched</span>
                        @elseif ($status === DispatchWorkflow::STATUS_READY)
                            <span class="dp-status dp-status--blue">Ready</span>
                        @else
                            <span class="dp-status dp-status--pending">Pending</span>
                        @endif
                    </td>
                    <td class="dp-actions">
                        @if ($status === DispatchWorkflow::STATUS_DISPATCHED)
                            <a href="{{ route('cms.orders.show', $order->id) }}" class="dp-link">View</a>
                        @elseif ($status === DispatchWorkflow::STATUS_READY)
                            <a href="{{ route('cms.dispatch.ship', $order->id) }}" class="cms-btn cms-btn--sm">Dispatch</a>
                        @else
                            <a href="{{ route('cms.dispatch.scan', $order->id) }}" class="cms-btn cms-btn--sm">Scan</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="dp-empty">No orders in this queue yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
