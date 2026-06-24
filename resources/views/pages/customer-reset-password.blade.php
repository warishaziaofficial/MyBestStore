@extends('layouts.app')

@section('title', 'MyBestStore | Reset Password')

@section('content')
<section class="mbs-page-section">
    <div class="mbs-container mx-auto max-w-md">
        <div class="rounded-2xl border border-border bg-white p-6 shadow-sm">
            <h1 class="text-xl font-bold text-navy">Reset your password</h1>
            <p class="mt-2 text-sm text-muted">Set a new password for <strong>{{ $email }}</strong></p>

            @if ($errors->any())
                <div class="mt-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('customer.password.update', $token) }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="text-sm font-medium text-foreground" for="reset-password">New password</label>
                    <input id="reset-password" type="password" name="password" required minlength="8" class="mbs-input mt-1" autocomplete="new-password">
                </div>
                <div>
                    <label class="text-sm font-medium text-foreground" for="reset-password-confirm">Confirm new password</label>
                    <input id="reset-password-confirm" type="password" name="password_confirmation" required minlength="8" class="mbs-input mt-1" autocomplete="new-password">
                </div>
                <p class="text-xs text-muted">Use at least 8 characters.</p>
                <button type="submit" class="mbs-btn mbs-btn-primary w-full">Update password</button>
            </form>
        </div>
    </div>
</section>
@endsection
