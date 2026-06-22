@php
    $cart = $cartSummary ?? ['items' => [], 'subtotal' => 0, 'shipping' => 0, 'discount' => 0, 'total' => 0, 'count' => 0];
@endphp

<div
    x-show="cartOpen"
    x-cloak
    class="cart-drawer-overlay"
    @keydown.escape.window="cartOpen = false"
>
    <div class="cart-drawer-backdrop" @click="cartOpen = false"></div>

    <div id="cart-drawer-panel">
        @include('components.cart-drawer-panel', ['cart' => $cart])
    </div>
</div>
