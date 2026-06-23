@extends('cms::layouts.admin')

@section('title', 'Dispatch Order')

@section('page_heading')
    <div class="dp-page-head">
        <a href="{{ route('cms.dispatch.scan', $order->id) }}" class="dp-back">← Back</a>
        <h1 class="sf-page-title">Dispatch Order</h1>
    </div>
@endsection

@section('content')
@php
    use Cms\Support\DispatchWorkflow;
@endphp

<div class="dp-ship-banner">
    <strong>All items verified and ready to ship</strong>
    <p>{{ $order->order_number }} — {{ $progress['total'] }} items scanned and confirmed for dispatch.</p>
</div>

<div class="dp-ship-grid">
    <div class="dp-ship-col">
        <div class="sf-card dp-card">
            <h2 class="dp-card-title">Ship To</h2>
            <dl class="dp-dl">
                <div><dt>Order #</dt><dd>{{ $order->order_number }}</dd></div>
                <div><dt>Customer</dt><dd>{{ $order->customer_name }}</dd></div>
                <div><dt>Address</dt><dd>{{ DispatchWorkflow::shippingAddressLine($order) }}</dd></div>
                <div><dt>Parcel size</dt><dd>{{ $progress['total'] }} pcs · {{ $order->items->count() }} products</dd></div>
            </dl>
        </div>

        <div class="sf-card dp-card">
            <h2 class="dp-card-title">Packed Items</h2>
            <table class="sf-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ DispatchWorkflow::productSku($item->product, $item->product_id) }}</td>
                            <td>{{ $item->quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="dp-ship-col">
        <form method="POST" action="{{ route('cms.dispatch.ship.confirm', $order->id) }}" class="sf-card dp-card dp-ship-form" id="dp-ship-form">
            @csrf
            <h2 class="dp-card-title">Courier Service</h2>

            @if (session('error'))
                <div class="sf-alert sf-alert-error">{{ session('error') }}</div>
            @endif

            <label class="cms-label" for="courier_key">Select courier</label>
            <select id="courier_key" name="courier_key" class="cms-input" required>
                <option value="" disabled selected>Select a courier...</option>
                @foreach ($couriers as $courier)
                    <option value="{{ $courier['key'] }}" @selected(old('courier_key') === $courier['key'])>{{ $courier['name'] }}</option>
                @endforeach
            </select>

            <div class="dp-tracking-row">
                <label class="cms-label" for="tracking_number">Parcel / tracking number</label>
                <button type="button" class="dp-auto-link" id="dp-auto-tracking">Auto-generate</button>
            </div>
            <input type="text" id="tracking_number" name="tracking_number" class="cms-input" value="{{ old('tracking_number') }}" placeholder="Enter tracking number" required disabled>

            <p id="dp-tracking-summary" class="dp-tracking-summary" hidden></p>

            <button type="submit" class="cms-btn cms-btn--block" id="dp-confirm-dispatch" disabled>Confirm Dispatch</button>
            <p class="dp-help dp-help--center">Select a courier and enter a parcel number to enable dispatch.</p>
        </form>
    </div>
</div>

<script src="{{ asset('assets/cms/js/dispatch-ship.js') }}?v={{ @filemtime(public_path('assets/cms/js/dispatch-ship.js')) ?: 1 }}" defer></script>
@endsection
