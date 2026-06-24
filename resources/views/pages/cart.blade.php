@extends('layouts.app')
@php use App\Support\Mbs; @endphp

@section('title', 'DigitalWares | Cart')

@section('content')
<section class="cart-hero">
    <div class="mbs-container cart-hero-inner">
        <h1 class="cart-hero-title">Your Shopping Cart</h1>
        <nav class="cart-hero-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span aria-hidden="true">&gt;</span>
            <span aria-current="page">Your Shopping Cart</span>
        </nav>
    </div>
</section>

<section class="cart-page">
    <div class="mbs-container cart-shell">
        @if (empty($cart['items']))
            <div class="cart-empty">
                <h2>Your cart is empty</h2>
                <p>Review your items before checkout — add products from the shop to continue.</p>
                <a href="{{ route('shop') }}" class="cart-checkout-btn cart-checkout-btn--inline">Continue Shopping</a>
            </div>
        @else
            <div class="cart-page-layout">
                <div class="cart-page-main">
                    <div class="cart-table" role="table" aria-label="Cart items">
                        <div class="cart-table-head" role="row">
                            <div class="cart-table-col cart-table-col--product" role="columnheader">Product</div>
                            <div class="cart-table-col cart-table-col--qty" role="columnheader">Quantity</div>
                            <div class="cart-table-col cart-table-col--total" role="columnheader">Total</div>
                        </div>

                        @foreach ($cart['items'] as $item)
                            <div class="cart-row" role="row">
                                <div class="cart-table-col cart-table-col--product" role="cell">
                                    <a href="{{ route('product.show', $item['slug']) }}" class="cart-row-image">
                                        <img src="{{ Mbs::image($item['image']) }}" alt="{{ $item['name'] }}">
                                    </a>
                                    <div class="cart-product-info">
                                        <a href="{{ route('product.show', $item['slug']) }}" class="cart-row-title">{{ $item['name'] }}</a>
                                        <p class="cart-row-price">{{ Mbs::price($item['price']) }}</p>
                                        <form action="{{ route('cart.remove') }}" method="POST" class="cart-row-remove-form">
                                            @csrf
                                            <input type="hidden" name="slug" value="{{ $item['slug'] }}">
                                            <button type="submit" class="cart-row-remove">Remove</button>
                                        </form>
                                    </div>
                                </div>

                                <div class="cart-table-col cart-table-col--qty" role="cell">
                                    <span class="cart-mobile-label">Quantity</span>
                                    <div class="cart-qty-control">
                                        <form action="{{ route('cart.update') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="slug" value="{{ $item['slug'] }}">
                                            <input type="hidden" name="quantity" value="{{ max(1, $item['quantity'] - 1) }}">
                                            <button type="submit" class="cart-qty-btn" @disabled($item['quantity'] <= 1) aria-label="Decrease quantity">−</button>
                                        </form>
                                        <span class="cart-qty-value">{{ $item['quantity'] }}</span>
                                        <form action="{{ route('cart.update') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="slug" value="{{ $item['slug'] }}">
                                            <input type="hidden" name="quantity" value="{{ min(99, $item['quantity'] + 1) }}">
                                            <button type="submit" class="cart-qty-btn" @disabled($item['quantity'] >= 99) aria-label="Increase quantity">+</button>
                                        </form>
                                    </div>
                                </div>

                                <div class="cart-table-col cart-table-col--total" role="cell">
                                    <span class="cart-mobile-label">Total</span>
                                    <strong class="cart-row-total">{{ Mbs::price($item['price'] * $item['quantity']) }}</strong>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="cart-page-actions">
                        <a href="{{ route('shop') }}" class="cart-continue-btn">Continue Shopping</a>
                        <form action="{{ route('cart.clear') }}" method="POST">
                            @csrf
                            <button type="submit" class="cart-clear-link">Clear cart</button>
                        </form>
                    </div>

                    <div class="cart-note-panel">
                        <label for="cart_note" class="cart-note-label">Order note</label>
                        <textarea id="cart_note" class="cart-note-input" rows="4" placeholder="Special instructions for your order (saved at checkout)"></textarea>

                        <div class="cart-discount-row">
                            <x-discount-code-form :cart="$cart" redirect="cart" input-id="cart_discount" />
                        </div>
                    </div>
                </div>

                <aside class="cart-summary-panel" aria-labelledby="cart-summary-heading">
                    <h2 id="cart-summary-heading" class="cart-summary-heading">Order Summary</h2>
                    <div class="cart-summary-rows">
                        <div class="cart-summary-row">
                            <span>Subtotal</span>
                            <strong>{{ Mbs::price($cart['subtotal']) }}</strong>
                        </div>
                        <div class="cart-summary-row">
                            <span>Shipping</span>
                            <strong>{{ $cart['shipping'] > 0 ? Mbs::price($cart['shipping']) : 'Free' }}</strong>
                        </div>
                        <div class="cart-summary-row">
                            <span>Discount</span>
                            <strong>{{ $cart['discount'] > 0 ? '-'.Mbs::price($cart['discount']) : '—' }}</strong>
                        </div>
                        <div class="cart-summary-row cart-summary-row--total">
                            <span>Grand Total</span>
                            <strong>{{ Mbs::price($cart['total']) }}</strong>
                        </div>
                    </div>
                    <p class="cart-summary-note">Review your items before checkout. Taxes and shipping calculated at checkout.</p>
                    <a href="{{ route('checkout') }}" class="cart-checkout-btn">Proceed to Checkout</a>
                </aside>
            </div>
        @endif
    </div>
</section>

@if (! empty($cart['items']) && count($crossSellProducts ?? []))
@include('components.product-relations-section', [
    'title' => 'Complete Your Setup',
    'subtitle' => 'Related products based on items in your cart',
    'products' => $crossSellProducts,
    'gridClass' => 'mbs-product-grid--4',
])
@endif
@endsection
