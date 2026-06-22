@php use App\Support\Mbs; @endphp

<div class="mbs-cart-summary-rows">
    <div class="mbs-cart-summary-row">
        <span>Subtotal</span>
        <strong>{{ Mbs::price($cart['subtotal']) }}</strong>
    </div>
    <div class="mbs-cart-summary-row">
        <span>Shipping</span>
        <strong>{{ $cart['shipping'] > 0 ? Mbs::price($cart['shipping']) : 'Free' }}</strong>
    </div>
    <div class="mbs-cart-summary-row">
        <span>Discount</span>
        <strong>{{ $cart['discount'] > 0 ? '-'.Mbs::price($cart['discount']) : '—' }}</strong>
    </div>
    <div class="mbs-cart-summary-row mbs-cart-summary-row--total">
        <span>Grand Total</span>
        <strong>{{ Mbs::price($cart['total']) }}</strong>
    </div>
</div>
