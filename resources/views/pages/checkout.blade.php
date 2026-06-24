@extends('layouts.app')
@php
    use App\Services\CartService;
    use App\Support\Mbs;

    $fullName = old('customer_name', '');
    $nameParts = preg_split('/\s+/', trim($fullName), 2);
    $firstName = old('first_name', $nameParts[0] ?? '');
    $lastName = old('last_name', $nameParts[1] ?? '');
    $cartCount = app(CartService::class)->count();
    $estimatedTax = 0;
    $countries = [
        'Pakistan',
        'India',
        'United Arab Emirates',
        'Saudi Arabia',
        'United Kingdom',
        'United States',
    ];
    $selectedCountry = old('country', 'Pakistan');
    $checkoutTotals = $checkoutTotals ?? [
        'subtotal' => $cart['subtotal'],
        'shipping' => 0,
        'discount' => $cart['discount'],
        'total' => max(0, $cart['subtotal'] - $cart['discount']),
    ];
    $shippingQuote = $shippingQuote ?? ['available' => false, 'methods' => [], 'message' => null, 'zone' => null];
    $initialShippingMethod = old('shipping_method', $shippingQuote['methods'][0]['key'] ?? '');

    $checkoutPayments = [
        [
            'key' => 'cash_on_delivery',
            'label' => 'Cash on Delivery',
            'description' => 'Pay when your order is delivered.',
            'chips' => [['logo' => 'cod']],
        ],
        [
            'key' => 'jazzcash',
            'label' => 'JazzCash',
            'description' => 'Pay via JazzCash mobile wallet',
            'chips' => [['logo' => 'jazzcash']],
        ],
    ];
@endphp

@section('title', 'DigitalWares | Checkout')

@section('content')
<section class="checkout-shell">
    <div class="checkout-toolbar">
        <a href="{{ route('home') }}" class="checkout-toolbar-brand">
            <img src="{{ asset('logo.png') }}" alt="DigitalWares" class="checkout-toolbar-logo">
        </a>
        <a href="{{ route('cart') }}" class="checkout-toolbar-cart" aria-label="View cart">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-width="2" d="M6 7h15l-1.5 9h-12z"/>
                <circle cx="9" cy="20" r="1"/>
                <circle cx="18" cy="20" r="1"/>
            </svg>
            @if ($cartCount > 0)
                <span class="checkout-toolbar-cart-count">{{ $cartCount }}</span>
            @endif
        </a>
    </div>

    <form
        action="{{ route('checkout.store') }}"
        method="POST"
        class="checkout-layout"
        x-data="{
            paymentMethod: @js(old('payment_method', 'cash_on_delivery') === 'cod' ? 'cash_on_delivery' : old('payment_method', 'cash_on_delivery')),
            country: @js(old('country', 'Pakistan')),
            shippingAddress: @js(old('shipping_address', '')),
            city: @js(old('city', '')),
            province: @js(old('province', '')),
            postalCode: @js(old('postal_code', '')),
            shippingMethod: @js($initialShippingMethod),
            shippingMethods: @js($shippingQuote['methods'] ?? []),
            shippingAvailable: @js($shippingQuote['available'] ?? false),
            shippingMessage: @js($shippingQuote['message'] ?? null),
            shippingZone: @js($shippingQuote['zone'] ?? null),
            shippingLoading: false,
            subtotal: {{ (int) $checkoutTotals['subtotal'] }},
            discount: {{ (int) $checkoutTotals['discount'] }},
            shippingAmount: {{ (int) $checkoutTotals['shipping'] }},
            grandTotal: {{ (int) $checkoutTotals['total'] }},
            quoteUrl: @js(route('checkout.shipping-quote')),
            get hasShippingAddress() {
                return this.country.trim() !== '' && this.city.trim() !== '' && this.province.trim() !== '';
            },
            formatPrice(amount) {
                return 'Rs ' + Number(amount || 0).toLocaleString('en-PK');
            },
            syncTotals() {
                const selected = this.shippingMethods.find((method) => method.key === this.shippingMethod);
                this.shippingAmount = selected ? Number(selected.amount || 0) : 0;
                this.grandTotal = Math.max(0, this.subtotal + this.shippingAmount - this.discount);
            },
            selectShippingMethod(key) {
                this.shippingMethod = key;
                this.syncTotals();
            },
            async fetchShippingMethods() {
                if (! this.hasShippingAddress) {
                    this.shippingMethods = [];
                    this.shippingAvailable = false;
                    this.shippingMessage = 'Enter your delivery address to view shipping methods.';
                    this.shippingMethod = '';
                    this.shippingAmount = 0;
                    this.grandTotal = Math.max(0, this.subtotal - this.discount);
                    return;
                }

                this.shippingLoading = true;

                try {
                    const params = new URLSearchParams({
                        country: this.country,
                        province: this.province,
                        city: this.city,
                    });
                    const response = await fetch(`${this.quoteUrl}?${params.toString()}`, {
                        headers: { 'Accept': 'application/json' },
                    });

                    if (! response.ok) {
                        throw new Error('Unable to load shipping methods.');
                    }

                    const data = await response.json();
                    this.shippingMethods = data.methods || [];
                    this.shippingAvailable = Boolean(data.available);
                    this.shippingMessage = data.message || null;
                    this.shippingZone = data.zone || null;

                    if (! this.shippingMethods.some((method) => method.key === this.shippingMethod)) {
                        this.shippingMethod = this.shippingMethods[0]?.key || '';
                    }

                    this.syncTotals();
                } catch (error) {
                    this.shippingMethods = [];
                    this.shippingAvailable = false;
                    this.shippingMessage = 'Shipping is not available for this location. Please contact support.';
                    this.shippingMethod = '';
                    this.shippingAmount = 0;
                    this.grandTotal = Math.max(0, this.subtotal - this.discount);
                } finally {
                    this.shippingLoading = false;
                }
            }
        }"
        x-init="
            if (hasShippingAddress) {
                fetchShippingMethods();
            }
        "
        @submit="
            const first = document.getElementById('checkout_first_name').value.trim();
            const last = document.getElementById('checkout_last_name').value.trim();
            document.getElementById('checkout_customer_name').value = [first, last].filter(Boolean).join(' ');
        "
    >
        @csrf
        <input type="hidden" name="customer_name" id="checkout_customer_name" value="{{ old('customer_name') }}">

        <div class="checkout-main">
            <div class="checkout-form-section checkout-contact">
                <div class="checkout-section-head">
                    <h2>Contact</h2>
                    <button type="button" class="checkout-signin-link" @click="loginOpen = true">Sign in</button>
                </div>
                <div class="checkout-field">
                    <label for="customer_email" class="checkout-label">Email or mobile phone number</label>
                    <input id="customer_email" type="email" name="customer_email" value="{{ old('customer_email') }}" required class="checkout-input">
                </div>
                <div class="checkout-field">
                    <label for="customer_phone" class="checkout-label">Phone number</label>
                    <input id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required class="checkout-input">
                </div>
                <label class="checkout-checkbox">
                    <input type="checkbox" name="marketing_opt_in" value="1">
                    <span>Email me with news and offers</span>
                </label>
            </div>

            <div class="checkout-form-section checkout-delivery">
                <h2>Delivery</h2>
                <div class="checkout-field">
                    <label for="country" class="checkout-label">Country / Region</label>
                    <select id="country" name="country" class="checkout-input checkout-select" required x-model="country" @change="fetchShippingMethods()">
                        <option value="" disabled @selected(! $selectedCountry)>Select Country/Region</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country }}" @selected($selectedCountry === $country)>{{ $country }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="checkout-field-grid checkout-field-grid--2">
                    <div class="checkout-field">
                        <label for="checkout_first_name" class="checkout-label">First name</label>
                        <input id="checkout_first_name" type="text" value="{{ $firstName }}" class="checkout-input" required>
                    </div>
                    <div class="checkout-field">
                        <label for="checkout_last_name" class="checkout-label">Last name</label>
                        <input id="checkout_last_name" type="text" value="{{ $lastName }}" class="checkout-input" required>
                    </div>
                </div>
                <div class="checkout-field">
                    <label for="shipping_address" class="checkout-label">Address</label>
                    <input id="shipping_address" name="shipping_address" value="{{ old('shipping_address') }}" required class="checkout-input" x-model="shippingAddress">
                </div>
                <div class="checkout-field">
                    <label for="apartment" class="checkout-label">Apartment, suite, etc. (optional)</label>
                    <input id="apartment" type="text" class="checkout-input" placeholder="Apartment, suite, unit, etc.">
                </div>
                <div class="checkout-field-grid checkout-field-grid--3">
                    <div class="checkout-field">
                        <label for="city" class="checkout-label">City</label>
                        <input id="city" name="city" value="{{ old('city') }}" required class="checkout-input" x-model="city" @input.debounce.400ms="fetchShippingMethods()">
                    </div>
                    <div class="checkout-field">
                        <label for="province" class="checkout-label">Province</label>
                        <input id="province" name="province" value="{{ old('province') }}" required class="checkout-input" x-model="province" @input.debounce.400ms="fetchShippingMethods()">
                    </div>
                    <div class="checkout-field">
                        <label for="postal_code" class="checkout-label">Postal code (optional)</label>
                        <input id="postal_code" name="postal_code" value="{{ old('postal_code') }}" class="checkout-input" x-model="postalCode">
                    </div>
                </div>
                <label class="checkout-checkbox">
                    <input type="checkbox" name="save_info" value="1">
                    <span>Save this information for next time</span>
                </label>
            </div>

            <div class="checkout-form-section checkout-shipping">
                <h2>Shipping method</h2>

                <div class="checkout-shipping-placeholder" x-show="!hasShippingAddress && !shippingLoading">
                    Enter your country, province, and city to view available shipping methods.
                </div>

                <div class="checkout-shipping-placeholder" x-show="shippingLoading" x-cloak>
                    Calculating shipping options...
                </div>

                <div class="checkout-shipping-error" x-show="hasShippingAddress && !shippingLoading && !shippingAvailable" x-cloak>
                    <span x-text="shippingMessage || 'Shipping is not available for this location. Please contact support.'"></span>
                </div>

                <div class="checkout-shipping-options" x-show="hasShippingAddress && !shippingLoading && shippingAvailable && shippingMethods.length" x-cloak>
                    <template x-for="method in shippingMethods" :key="method.key">
                        <label
                            class="checkout-shipping-option-card"
                            :class="{ 'is-active': shippingMethod === method.key }"
                        >
                            <input
                                type="radio"
                                name="shipping_method"
                                :value="method.key"
                                x-model="shippingMethod"
                                @change="selectShippingMethod(method.key)"
                                required
                            >
                            <div class="checkout-shipping-option-box">
                                <div class="checkout-shipping-option-top">
                                    <span class="checkout-payment-radio" aria-hidden="true"></span>
                                    <div class="checkout-shipping-option-copy">
                                        <strong x-text="method.name"></strong>
                                        <small x-text="method.estimate"></small>
                                        <small x-show="method.description" x-text="method.description"></small>
                                    </div>
                                    <strong class="checkout-shipping-option-price" x-text="method.formatted_amount || formatPrice(method.amount)"></strong>
                                </div>
                            </div>
                        </label>
                    </template>
                </div>
            </div>

            <div class="checkout-form-section checkout-payment">
                <h2>Payment</h2>
                <p class="checkout-payment-note">All transactions are secure and encrypted.</p>

                <div class="checkout-payment-options">
                    @foreach ($checkoutPayments as $payment)
                        <label
                            class="checkout-payment-option"
                            :class="{ 'is-active': paymentMethod === @js($payment['key']) }"
                        >
                            <input
                                type="radio"
                                name="payment_method"
                                value="{{ $payment['key'] }}"
                                x-model="paymentMethod"
                                @checked(old('payment_method', 'cash_on_delivery') === $payment['key'] || ($payment['key'] === 'cash_on_delivery' && old('payment_method') === 'cod'))
                                required
                            >
                            <div class="checkout-payment-option-box">
                                <div class="checkout-payment-option-top">
                                    <span class="checkout-payment-radio" aria-hidden="true"></span>
                                    <div class="checkout-payment-copy">
                                        <strong>{{ $payment['label'] }}</strong>
                                        <small>{{ $payment['description'] }}</small>
                                    </div>
                                    <div class="checkout-payment-icon">
                                        @foreach ($payment['chips'] as $chip)
                                            <x-payment-method-badge :method="$chip['logo']" variant="checkout" />
                                        @endforeach
                                    </div>
                                </div>

                                @if ($payment['key'] === 'jazzcash')
                                    <div class="checkout-payment-panel" x-show="paymentMethod === 'jazzcash'" x-cloak>
                                        <div class="checkout-field">
                                            <label for="jazzcash_account_number" class="checkout-label">JazzCash Mobile Number</label>
                                            <input id="jazzcash_account_number" type="text" name="payment_account_number" value="{{ old('payment_account_number') }}" class="checkout-input" placeholder="03XX XXXXXXX" :required="paymentMethod === 'jazzcash'" :disabled="paymentMethod !== 'jazzcash'">
                                        </div>
                                        <div class="checkout-field">
                                            <label for="jazzcash_account_name" class="checkout-label">Account Holder Name</label>
                                            <input id="jazzcash_account_name" type="text" name="payment_account_name" value="{{ old('payment_account_name') }}" class="checkout-input" placeholder="Full name on JazzCash account" :required="paymentMethod === 'jazzcash'" :disabled="paymentMethod !== 'jazzcash'">
                                        </div>
                                        <div class="checkout-field">
                                            <label for="jazzcash_reference" class="checkout-label">Transaction ID / Reference Number</label>
                                            <input id="jazzcash_reference" type="text" name="payment_reference" value="{{ old('payment_reference') }}" class="checkout-input" placeholder="Enter transaction reference" :required="paymentMethod === 'jazzcash'" :disabled="paymentMethod !== 'jazzcash'">
                                        </div>
                                        <p class="checkout-payment-panel-hint">Please send payment to our JazzCash account after order confirmation. Your order will be marked as pending until payment is verified.</p>
                                    </div>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="checkout-form-section checkout-submit-section">
                <div class="checkout-field">
                    <label for="notes" class="checkout-label">Order notes (optional)</label>
                    <textarea id="notes" name="notes" class="checkout-input checkout-textarea" rows="3" placeholder="Delivery instructions or special requests">{{ old('notes') }}</textarea>
                </div>
                <button type="submit" class="mbs-btn mbs-btn-primary checkout-place-order">
                    Place Order
                </button>
            </div>
        </div>

        <aside class="checkout-summary">
            <div class="checkout-summary-sticky">
                <div class="checkout-summary-list">
                    @foreach ($cart['items'] as $item)
                        <div class="checkout-summary-item">
                            <div class="checkout-summary-thumb">
                                <img src="{{ Mbs::image($item['image']) }}" alt="{{ $item['name'] }}">
                                <span class="checkout-qty-badge">{{ $item['quantity'] }}</span>
                            </div>
                            <div class="checkout-summary-item-body">
                                <p class="checkout-summary-item-title">{{ $item['name'] }}</p>
                                <p class="checkout-summary-item-meta">{{ $item['quantity'] }} × {{ Mbs::price($item['price']) }}</p>
                            </div>
                            <strong class="checkout-summary-item-price">{{ Mbs::price($item['price'] * $item['quantity']) }}</strong>
                        </div>
                    @endforeach
                </div>

                <x-discount-code-form :cart="$cart" redirect="checkout" input-id="checkout_discount" class="checkout-discount" />

                <div class="checkout-total-rows">
                    <div class="checkout-total-row">
                        <span>Subtotal</span>
                        <strong x-text="formatPrice(subtotal)">{{ Mbs::price($checkoutTotals['subtotal']) }}</strong>
                    </div>
                    <div class="checkout-total-row">
                        <span>Shipping</span>
                        <strong>
                            <template x-if="!hasShippingAddress">
                                <span class="checkout-total-muted">Enter delivery address</span>
                            </template>
                            <template x-if="hasShippingAddress && shippingAvailable && shippingMethod">
                                <span x-text="shippingAmount > 0 ? formatPrice(shippingAmount) : 'Free'"></span>
                            </template>
                            <template x-if="hasShippingAddress && !shippingAvailable && !shippingLoading">
                                <span class="checkout-total-muted">Unavailable</span>
                            </template>
                        </strong>
                    </div>
                    <div class="checkout-total-row">
                        <span>Discount</span>
                        <strong x-text="discount > 0 ? '-' + formatPrice(discount) : '—'">{{ $cart['discount'] > 0 ? '-'.Mbs::price($cart['discount']) : '—' }}</strong>
                    </div>
                    <div class="checkout-total-row checkout-total-row--grand">
                        <span>Total</span>
                        <strong class="checkout-total-amount">
                            <span class="checkout-total-currency">PKR</span>
                            <span x-text="formatPrice(grandTotal)">{{ Mbs::price($checkoutTotals['total']) }}</span>
                        </strong>
                    </div>
                </div>
            </div>
        </aside>
    </form>
</section>
@endsection
