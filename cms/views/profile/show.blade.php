@extends('cms::layouts.admin')

@section('title', 'My Profile')

@section('page_heading')
<div class="sf-page-banner__title-block">
    <h1 class="sf-page-title">My Profile</h1>
    <p class="sf-page-subtitle">Account details and password.</p>
</div>
@endsection

@section('content')
<div class="sf-form-layout sf-form-layout--standard">
    <div class="sf-form-columns">
        <div class="sf-form-columns__main">
            <section class="sf-panel sf-form-section sf-form-card">
            <header class="sf-form-section-head">
                <h2>Account</h2>
            </header>
            <div class="sf-form-grid sf-form-grid--media">
                <div class="sf-form-field sf-form-field--full">
                    <div class="sf-profile-card-head">
                        <div class="sf-profile-avatar sf-profile-avatar--lg">{{ strtoupper(substr($user->username, 0, 1)) }}</div>
                        <div>
                            <strong style="font-size:1.05rem;">{{ $user->username }}</strong>
                            <p class="sf-form-section-desc" style="margin-top:4px;">{{ $user->email }}</p>
                            <span class="sf-pill sf-pill--blue" style="margin-top:8px;display:inline-block;">{{ ucfirst($user->role) }}</span>
                        </div>
                    </div>
                </div>
                <dl class="cms-dl sf-profile-dl">
                    <dt>User ID</dt><dd>#{{ $user->id }}</dd>
                    <dt>Email</dt><dd>{{ $user->email }}</dd>
                    <dt>Role</dt><dd>{{ ucfirst($user->role) }}</dd>
                    @if ($user->created_at)
                        <dt>Member since</dt><dd>{{ $user->created_at->format('M j, Y') }}</dd>
                    @endif
                </dl>
                @if ($isAdmin)
                    <a href="{{ route('cms.resource.edit', ['users', $user->id]) }}" class="sf-btn sf-btn-outline sf-btn--sm">Edit account (Users)</a>
                @endif
            </div>
        </section>
        </div>

        <aside class="sf-form-columns__side">
            <section class="sf-panel sf-form-section sf-form-card">
            <header class="sf-form-section-head">
                <h2>Change password</h2>
            </header>
            <form method="POST" action="{{ route('cms.profile.password') }}" class="sf-form">
                @csrf
                @method('PUT')
                <div class="sf-form-grid sf-form-grid--media">
                    <div class="sf-form-field sf-form-field--full">
                        <label class="sf-form-label" for="sf-profile-current">Current password</label>
                        <input type="password" id="sf-profile-current" class="sf-input" name="current_password" required autocomplete="current-password">
                        @error('current_password')
                            <p class="sf-form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sf-form-field sf-form-field--full">
                        <label class="sf-form-label" for="sf-profile-password">New password</label>
                        <input type="password" id="sf-profile-password" class="sf-input" name="password" required autocomplete="new-password" minlength="8">
                    </div>
                    <div class="sf-form-field sf-form-field--full">
                        <label class="sf-form-label" for="sf-profile-password-confirm">Confirm new password</label>
                        <input type="password" id="sf-profile-password-confirm" class="sf-input" name="password_confirmation" required autocomplete="new-password">
                        @error('password')
                            <p class="sf-form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="sf-form-actions sf-form-actions--product">
                    <button type="submit" class="sf-btn sf-btn-primary sf-btn--sm">Update password</button>
                </div>
            </form>
            </section>
        </aside>
    </div>
</div>
@endsection
