@extends('cms::layouts.admin')

@section('title', 'Login')

@section('content')
<div class="cms-login-page">
    <div class="cms-login-hero">
        <img src="{{ asset('logo.png') }}" alt="MyBestStore" class="cms-login-hero-logo">
        <h1>MyBestStore Admin</h1>
        <p>Manage your ecommerce store — products, orders, content and customers from one place.</p>
        <ul class="cms-login-features">
            <li>Dashboard with store overview</li>
            <li>Products, orders &amp; inventory</li>
            <li>Social media order sync</li>
            <li>Homepage, blog &amp; media library</li>
        </ul>
    </div>
    <div class="cms-login-panel">
        <form method="POST" action="{{ route('cms.login.submit') }}" class="cms-login-form">
            @csrf
            <h2>Sign in to CMS</h2>
            <p class="cms-muted">Enter your admin credentials</p>

            @if ($errors->any())
                <div class="cms-alert cms-alert-error">{{ $errors->first() }}</div>
            @endif

            <label>
                Email address
                <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@mybeststore.pk">
            </label>
            <label>
                Password
                <input type="password" name="password" required placeholder="Your password">
            </label>
            <button type="submit" class="sf-btn sf-btn-primary sf-btn-block">Sign In</button>

            @if ($canRegister ?? false)
                <p class="cms-auth-switch">
                    No account yet? <a href="{{ route('cms.register') }}">Create admin account</a>
                </p>
            @endif

            <a href="{{ route('home') }}" class="cms-login-store-link">← Back to storefront</a>
        </form>
    </div>
</div>
@endsection
