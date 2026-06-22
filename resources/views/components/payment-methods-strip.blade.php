@props([
    'variant' => 'default',
])

<div {{ $attributes->class([]) }} aria-label="Accepted payment methods">
    <x-payment-method-badge method="jazzcash" :variant="$variant" />
    <x-payment-method-badge method="cod" :variant="$variant" />
</div>
