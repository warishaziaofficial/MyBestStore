@extends('cms::layouts.admin')

@section('title', 'Scan Items')

@section('page_heading')
<div class="sf-page-banner__title-block">
    <div class="dp-page-head-row">
        <h1 class="sf-page-title">Scan Items</h1>
        <span @class(['dp-badge', 'dp-badge--pending' => $status !== 'ready', 'dp-badge--ready' => $status === 'ready'])>
            {{ $status === 'ready' ? 'Ready' : 'Pending' }}
        </span>
    </div>
</div>
@endsection

@section('page_actions')
<a href="{{ route('cms.dispatch.queue') }}" class="sf-btn sf-btn-outline sf-btn--sm">← Back</a>
@endsection

@section('content')
@php
    use Cms\Support\DispatchWorkflow;
    use Cms\Support\ProductQr;
@endphp

<div class="dp-grid-2">
    <div class="sf-card dp-card">
        <h2 class="dp-card-title">Order Info</h2>
        <dl class="dp-dl">
            <div><dt>Order #</dt><dd>{{ $order->order_number }}</dd></div>
            <div><dt>Customer</dt><dd>{{ $order->customer_name }}</dd></div>
            <div><dt>Ship to</dt><dd>{{ DispatchWorkflow::shippingAddressLine($order) }}</dd></div>
            <div><dt>Order date</dt><dd>{{ $order->created_at?->format('M j, Y') }}</dd></div>
        </dl>
    </div>
    <div class="sf-card dp-card">
        <h2 class="dp-card-title">Scan Progress</h2>
        <p class="dp-progress-label"><span id="dp-progress-count">{{ $progress['scanned'] }}</span> of {{ $progress['total'] }} items</p>
        <div class="dp-progress-bar"><div id="dp-progress-fill" class="dp-progress-fill" style="width: {{ $progress['percent'] }}%"></div></div>
        <div class="dp-progress-meta">
            <span id="dp-progress-percent">{{ $progress['percent'] }}% complete</span>
            <span id="dp-progress-remaining">{{ $progress['remaining'] }} remaining</span>
        </div>
    </div>
</div>

<div class="sf-card dp-card">
    <h2 class="dp-card-title">QR / Barcode Scanner</h2>
    <p class="dp-help">Scan the <strong>QR or barcode from the table below</strong> (not the retail box UPC). Handheld scanners: scan then press Enter. You can also click <strong>Scan</strong> on a row.</p>

    <div class="dp-qr-camera-wrap">
        <button type="button" class="cms-btn cms-btn--ghost cms-btn--sm" id="dp-camera-toggle">Use camera</button>
        <div id="dp-qr-reader" class="dp-qr-reader" hidden></div>
    </div>

    <form id="dp-scan-form" class="dp-scan-form" data-url="{{ route('cms.dispatch.scan-barcode', $order->id) }}">
        @csrf
        <input type="text" id="dp-barcode-input" class="cms-input dp-scan-input" placeholder="Scan QR / barcode from table, or type code (e.g. MBS-PR-00020)…" autocomplete="off" autofocus>
        <button type="submit" class="cms-btn">Scan</button>
    </form>
    <div id="dp-scan-feedback" class="dp-scan-feedback" hidden></div>
</div>

<div class="sf-card dp-card">
    <h2 class="dp-card-title">Items in Order</h2>
    <table class="sf-table dp-items-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th>QR Code</th>
                <th>Barcode</th>
                <th>Qty</th>
                <th>Scanned</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="dp-items-body">
            @foreach ($order->items as $item)
                @php
                    $scanned = DispatchWorkflow::scannedQty($order, $item);
                    $done = $scanned >= (int) $item->quantity;
                    $qrCode = ProductQr::code($item->product, $item->product_id);
                    $qrUrl = ProductQr::imageUrl($item->product, $item->product_id, 88);
                    $barcodeCode = DispatchWorkflow::productBarcode($item->product, $item->product_id);
                    $barcodeSvg = DispatchWorkflow::productBarcodeSvg($item->product, $item->product_id);
                @endphp
                <tr data-item-id="{{ $item->id }}" data-qr-code="{{ $qrCode }}" data-barcode-code="{{ $barcodeCode }}" @class(['dp-item-row--done' => $done])>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ DispatchWorkflow::productSku($item->product, $item->product_id) }}</td>
                    <td class="dp-qr-cell">
                        @if ($qrUrl)
                            <img src="{{ $qrUrl }}" alt="QR {{ $qrCode }}" class="dp-qr-thumb" width="88" height="88" loading="lazy">
                            <code class="dp-qr-code">{{ $qrCode }}</code>
                        @endif
                    </td>
                    <td class="dp-barcode-cell">
                        @if ($barcodeSvg)
                            <div class="dp-barcode-thumb">{!! $barcodeSvg !!}</div>
                            <code class="dp-qr-code">{{ $barcodeCode }}</code>
                        @endif
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td class="dp-scanned-cell"><span class="dp-scanned-count">{{ $scanned }}</span> / {{ $item->quantity }}</td>
                    <td>
                        @if ($done)
                            <span class="dp-verified">Verified</span>
                        @else
                            <button type="button" class="cms-btn cms-btn--ghost cms-btn--sm dp-row-scan" data-url="{{ route('cms.dispatch.scan-item', [$order->id, $item->id]) }}">Scan</button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if ($progress['percent'] >= 100)
    <div class="dp-ready-banner">
        <div>
            <strong>All items verified and ready to ship</strong>
            <p>{{ $order->order_number }} — {{ $progress['total'] }} items scanned and confirmed for dispatch.</p>
        </div>
        <a href="{{ route('cms.dispatch.ship', $order->id) }}" class="cms-btn">Proceed to Dispatch →</a>
    </div>
@else
    <div id="dp-ready-banner" class="dp-ready-banner" hidden>
        <div>
            <strong>All items verified and ready to ship</strong>
            <p>{{ $order->order_number }} — <span id="dp-ready-count">{{ $progress['total'] }}</span> items scanned and confirmed for dispatch.</p>
        </div>
        <a href="{{ route('cms.dispatch.ship', $order->id) }}" class="cms-btn">Proceed to Dispatch →</a>
    </div>
@endif

<script>
window.cmsDispatchScan = {
    csrf: @json(csrf_token()),
    shipUrl: @json(route('cms.dispatch.ship', $order->id)),
};
</script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js" defer></script>
<script src="{{ asset('assets/cms/js/dispatch.js') }}?v={{ @filemtime(public_path('assets/cms/js/dispatch.js')) ?: 1 }}" defer></script>
@endsection
