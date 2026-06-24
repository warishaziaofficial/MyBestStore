@extends('cms::layouts.admin')

@section('title', 'Customer Password Reset')

@section('page_heading')
<div class="sf-page-banner__title-block">
    <h1 class="sf-page-title">Customer Password Reset</h1>
    <p class="sf-page-subtitle">Set a new password for a storefront customer. The customer and admin team are emailed automatically.</p>
</div>
@endsection

@section('content')
<div class="sf-form-layout sf-form-layout--standard">
    <section class="sf-panel sf-form-section sf-form-card sf-password-reset-card">
        <header class="sf-form-section-head">
            <h2>Reset customer password</h2>
            <p class="sf-form-section-desc">When a customer requests a reset on the storefront, you will receive an email with a link to this page. After you save a new password, the customer receives their new password plus a link to choose their own password.</p>
        </header>
        <form method="POST" action="{{ route('cms.customers.password-reset.set') }}" class="sf-form">
            @csrf
            <div class="sf-form-grid sf-form-grid--media">
                <div class="sf-form-field sf-form-field--full">
                    <label class="sf-form-label" for="sf-reset-set-customer">Customer</label>
                    <select id="sf-reset-set-customer" class="sf-input sf-select" name="customer_id" required>
                        <option value="">Select customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>
                                {{ $customer->email }}@if ($customer->name) — {{ $customer->name }}@endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="sf-form-field sf-form-field--full">
                    <label class="sf-form-label" for="sf-reset-password">New password</label>
                    <input type="password" id="sf-reset-password" class="sf-input" name="password" value="{{ old('password') }}" required minlength="8" autocomplete="new-password">
                </div>
                <div class="sf-form-field sf-form-field--full">
                    <label class="sf-form-label" for="sf-reset-password-confirm">Confirm password</label>
                    <input type="password" id="sf-reset-password-confirm" class="sf-input" name="password_confirmation" required minlength="8" autocomplete="new-password">
                </div>
            </div>
            <div class="sf-form-actions sf-form-actions--product">
                <button type="submit" class="sf-btn sf-btn-primary sf-btn--sm">Reset password &amp; notify</button>
            </div>
        </form>
    </section>
</div>
@endsection
