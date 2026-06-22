@php
    use App\Support\Mbs;
    $cart = $cart ?? ['items' => [], 'subtotal' => 0, 'shipping' => 0, 'discount' => 0, 'total' => 0, 'count' => 0];
    $freeShippingThreshold = (int) config('cart.free_shipping_threshold', 10000);
    $shippingProgress = $freeShippingThreshold > 0 ? min(100, round(($cart['subtotal'] / $freeShippingThreshold) * 100)) : 100;
@endphp

<aside
    class="cart-drawer"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
>
    <div class="cart-drawer-header">
        <h2 class="cart-drawer-title">Shopping cart</h2>
        <button type="button" @click="cartOpen = false" class="cart-drawer-close" aria-label="Close cart">✕</button>
    </div>

    @if (!empty($cart['items']))
        <div class="cart-drawer-shipping">
            @if ($cart['subtotal'] >= $freeShippingThreshold)
                <p class="cart-drawer-shipping-text">You are eligible for <strong>free shipping!</strong></p>
            @else
                <p class="cart-drawer-shipping-text">Add <strong>{{ Mbs::price(max(0, $freeShippingThreshold - $cart['subtotal'])) }}</strong> more for free shipping</p>
            @endif
            <div class="cart-drawer-progress" aria-hidden="true">
                <span class="cart-drawer-progress-bar" style="width: {{ $shippingProgress }}%"></span>
            </div>
        </div>
    @endif

    <div class="cart-drawer-body">
        @if (empty($cart['items']))
            <div class="cart-drawer-empty">
                <p>Your cart is empty</p>
                <p class="cart-drawer-empty-sub">Add products from the shop to see them here.</p>
                <a href="{{ route('shop') }}" @click="cartOpen = false" class="mbs-btn mbs-btn-primary">Browse Shop</a>
            </div>
        @else
            <div class="cart-drawer-items">
                @foreach ($cart['items'] as $item)
                    <div class="cart-drawer-row">
                        <a href="{{ route('product.show', $item['slug']) }}" @click="cartOpen = false" class="cart-drawer-row-image">
                            <img src="{{ Mbs::image($item['image']) }}" alt="{{ $item['name'] }}">
                        </a>
                        <div class="cart-drawer-row-content">
                            <div class="cart-drawer-row-top">
                                <a href="{{ route('product.show', $item['slug']) }}" @click="cartOpen = false" class="cart-drawer-row-title">{{ $item['name'] }}</a>
                                <form action="{{ route('cart.remove') }}" method="POST" class="cart-drawer-remove-form">
                                    @csrf
                                    <input type="hidden" name="slug" value="{{ $item['slug'] }}">
                                    <button type="submit" class="cart-drawer-remove" aria-label="Remove item">×</button>
                                </form>
                            </div>
                            <p class="cart-drawer-row-price">{{ Mbs::price($item['price'] * $item['quantity']) }}</p>
                            <p class="cart-drawer-row-meta">{{ $item['quantity'] }} × {{ Mbs::price($item['price']) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="cart-drawer-footer">
        <div class="cart-drawer-subtotal">
            <span>Subtotal</span>
            <strong>{{ Mbs::price($cart['subtotal']) }}</strong>
        </div>
        <p class="cart-drawer-footer-note">Taxes and shipping calculated at checkout</p>
        <a href="{{ route('checkout') }}" @click="cartOpen = false" @class(['mbs-btn mbs-btn-primary cart-drawer-checkout', 'pointer-events-none opacity-50' => empty($cart['items'])])>Check Out</a>
        <a href="{{ route('cart') }}" @click="cartOpen = false" class="mbs-btn mbs-btn-outline cart-drawer-view">View Cart</a>
    </div>
</aside>
