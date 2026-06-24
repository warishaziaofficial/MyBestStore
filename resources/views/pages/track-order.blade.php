@extends('layouts.app')

@section('title', 'DigitalWares | Track Order')

@section('content')
<section class="track-order-page">
    <div class="mbs-container track-order-shell">
        <div class="track-order-card">
            <div class="track-order-head">
                <h1>Track Your Order</h1>
                <p>Enter your order number and phone number to check delivery status.</p>
            </div>

            @if (session('error'))
                <div class="track-order-alert track-order-alert--error">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('track-order.lookup') }}" class="track-order-form">
                @csrf
                <div class="track-order-field">
                    <label for="track_order_number">Order Number or Barcode</label>
                    <input
                        id="track_order_number"
                        type="text"
                        name="order_number"
                        value="{{ old('order_number') }}"
                        class="mbs-input"
                        placeholder="e.g. MBS-20260622-ABC123 or MBS-ORD-00014"
                        required
                    >
                </div>

                <div class="track-order-field">
                    <label for="track_phone">Phone Number</label>
                    <input
                        id="track_phone"
                        type="text"
                        name="phone"
                        value="{{ old('phone') }}"
                        class="mbs-input"
                        placeholder="03XX XXXXXXX"
                    >
                </div>

                <div class="track-order-divider"><span>or</span></div>

                <div class="track-order-field">
                    <label for="track_email">Email Address</label>
                    <input
                        id="track_email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="mbs-input"
                        placeholder="you@example.com"
                    >
                </div>

                <button type="submit" class="mbs-btn mbs-btn-primary track-order-submit">Track Order</button>
            </form>
        </div>
    </div>
</section>
@endsection
