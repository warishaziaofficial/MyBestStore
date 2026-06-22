@props([
    'order',
    'variant' => 'large',
])

@php
    use App\Services\BarcodeService;

    $code = $order->order_barcode ?: $order->order_number;
    $barcodeUrl = filled($order->order_barcode)
        ? route('admin.orders.barcode', $order)
        : null;
    $isCompact = $variant === 'compact';
    $qrSize = $isCompact ? 80 : 160;
    $qrUrl = app(BarcodeService::class)->qrImageUrl($code, $qrSize);
@endphp

<div {{ $attributes->merge(['class' => 'order-barcode-block order-barcode-block--'.$variant]) }}>
    <div class="order-barcode-grid">
        <div class="order-barcode-visual order-barcode-visual--qr">
            <img src="{{ $qrUrl }}" alt="Order QR code {{ $code }}" class="order-barcode-qr">
        </div>
        @if ($barcodeUrl)
            <div class="order-barcode-visual order-barcode-visual--barcode">
                <img src="{{ $barcodeUrl }}" alt="Order barcode {{ $code }}" class="order-barcode-image">
            </div>
        @endif
    </div>
    <p class="order-barcode-code">{{ $code }}</p>
</div>
