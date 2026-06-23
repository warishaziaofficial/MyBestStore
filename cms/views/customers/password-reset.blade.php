@extends('cms::layouts.admin')

@section('title', 'Customer Password Reset')

@section('page_heading')
<div>
    <h1 class="sf-page-title">Customer Password Reset</h1>
    <p class="sf-page-subtitle">Send a reset email or set a new password for storefront customers.</p>
</div>
@endsection

@section('content')
<div class="cms-detail-grid">
    <div class="cms-panel">
        <h2>Send Reset Email</h2>
        <form method="POST" action="{{ route('cms.customers.password-reset.send') }}" class="cms-form">
            @csrf
            <label>
                Customer
                <select name="customer_id" required>
                    <option value="">Select customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->email }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" class="cms-btn cms-btn-primary">Send Reset Link</button>
        </form>
    </div>

    <div class="cms-panel">
        <h2>Set Password Manually</h2>
        <form method="POST" action="{{ route('cms.customers.password-reset.set') }}" class="cms-form">
            @csrf
            <label>
                Customer
                <select name="customer_id" required>
                    <option value="">Select customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->email }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                New password
                <input type="password" name="password" required minlength="8">
            </label>
            <label>
                Confirm password
                <input type="password" name="password_confirmation" required minlength="8">
            </label>
            <button type="submit" class="cms-btn cms-btn-primary">Update Password</button>
        </form>
    </div>
</div>
@endsection
