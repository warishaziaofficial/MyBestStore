@props([
    'cart' => ['coupon' => null],
    'redirect' => 'cart',
    'inputId' => 'discount_code',
])

<div {{ $attributes->merge(['class' => 'mbs-discount-code']) }}>
    <label for="{{ $inputId }}" class="mbs-discount-code-label">Discount code</label>

    @if (! empty($cart['coupon']))
        <div class="mbs-discount-code-applied">
            <span class="mbs-discount-code-badge">{{ $cart['coupon'] }}</span>
            <form action="{{ route('cart.coupon.remove') }}" method="POST" class="mbs-discount-code-remove-form">
                @csrf
                @method('DELETE')
                <input type="hidden" name="redirect" value="{{ $redirect }}">
                <button type="submit" class="mbs-discount-code-remove">Remove</button>
            </form>
        </div>
    @else
        <form action="{{ route('cart.coupon.apply') }}" method="POST" class="mbs-discount-code-form">
            @csrf
            <input type="hidden" name="redirect" value="{{ $redirect }}">
            <div class="mbs-discount-code-box">
                <input
                    id="{{ $inputId }}"
                    type="text"
                    name="coupon"
                    value="{{ old('coupon') }}"
                    class="mbs-discount-code-input"
                    placeholder="Enter discount code"
                    aria-label="Discount code"
                    autocomplete="off"
                >
                <button type="submit" class="mbs-discount-code-apply">Apply</button>
            </div>
        </form>
    @endif
</div>
