@props([
    'method',
    'variant' => 'default',
])

@php
    $labels = [
        'visa' => 'Visa',
        'mastercard' => 'Mastercard',
        'jazzcash' => 'JazzCash',
        'easypaisa' => 'EasyPaisa',
        'paypak' => 'PayPak',
        'bank' => 'Bank Transfer',
        'cod' => 'Cash on Delivery',
    ];

    $logoMethods = ['visa', 'mastercard', 'jazzcash', 'easypaisa', 'paypak'];
    $logoExtensions = [
        'jazzcash' => 'png',
    ];
    $label = $labels[$method] ?? ucfirst($method);
    $logoExtension = $logoExtensions[$method] ?? 'svg';
@endphp

<span {{ $attributes->class([
    'mbs-payment-badge',
    "mbs-payment-badge--{$method}",
    "mbs-payment-badge--{$variant}",
]) }}>
    @if ($method === 'cod')
        COD
    @elseif (in_array($method, $logoMethods, true))
        <img
            src="{{ asset("assets/images/payments/{$method}.{$logoExtension}") }}"
            alt="{{ $label }}"
            loading="lazy"
            decoding="async"
        >
    @else
        {{ strtoupper($method) }}
    @endif
</span>
