@extends('cms::layouts.admin')

@section('title', 'Register')

@section('content')
<div class="cms-login-page">
    <div class="cms-login-hero">
        <img src="{{ asset('logo.png') }}" alt="DigitalWares" class="cms-login-hero-logo">
        <h1>DigitalWares Admin</h1>
        <p>Create your admin account and start managing products, orders, and website content.</p>
        <ul class="cms-login-features">
            <li>Dashboard with sales overview</li>
            <li>Products — add, edit, delete</li>
            <li>Orders &amp; social integration</li>
            <li>Full CMS for homepage content</li>
        </ul>
    </div>
    <div class="cms-login-panel">
        @if (! $canRegister)
            <div class="cms-login-form">
                <h2>Registration closed</h2>
                <p class="cms-muted">An admin account already exists for this store. Please sign in, or ask the store admin to add your account under <strong>Users &amp; Roles</strong>.</p>
                <a href="{{ route('cms.login') }}" class="sf-btn sf-btn-primary sf-btn-block">Go to Sign In</a>
                <a href="{{ route('home') }}" class="cms-login-store-link">← Back to storefront</a>
            </div>
        @else
            <form method="POST" action="{{ route('cms.register.submit') }}" class="cms-login-form">
                @csrf
                <h2>Create admin account</h2>
                <p class="cms-muted">
                    @if ($isFirstAdmin)
                        First-time setup — this account will be the main admin.
                    @else
                        Register for CMS access.
                    @endif
                </p>

                @if ($errors->any())
                    <div class="cms-alert cms-alert-error">{{ $errors->first() }}</div>
                @endif

                <label>
                    Full name / Username
                    <input type="text" name="username" value="{{ old('username') }}" required autofocus placeholder="Your name">
                </label>
                <label>
                    Email address
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="admin@digitalwares.pk">
                </label>
                <label>
                    Password
                    <input type="password" name="password" required placeholder="Min. 8 characters">
                </label>
                <label>
                    Confirm password
                    <input type="password" name="password_confirmation" required placeholder="Repeat password">
                </label>
                <button type="submit" class="sf-btn sf-btn-primary sf-btn-block">Create Account</button>
                <p class="cms-auth-switch">
                    Already have an account? <a href="{{ route('cms.login') }}">Sign in</a>
                </p>
                <a href="{{ route('home') }}" class="cms-login-store-link">← Back to storefront</a>
            </form>
        @endif
    </div>
</div>
@endsection
