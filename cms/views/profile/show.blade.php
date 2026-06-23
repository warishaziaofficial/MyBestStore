@extends('cms::layouts.admin')

@section('title', 'My Profile')

@section('page_heading')
<h1 class="sf-page-title">My Profile</h1>
@endsection

@section('content')
<div class="cms-detail-grid">
    <div class="sf-panel">
        <div class="sf-profile-card-head">
            <div class="sf-profile-avatar sf-profile-avatar--lg">{{ strtoupper(substr($user->username, 0, 1)) }}</div>
            <div>
                <h2 style="margin:0;font-size:1.25rem;font-weight:800;">{{ $user->username }}</h2>
                <p class="cms-muted" style="margin:4px 0 0;">{{ $user->email }}</p>
                <span class="sf-pill sf-pill--blue" style="margin-top:8px;">{{ ucfirst($user->role) }}</span>
            </div>
        </div>
        <dl class="cms-dl" style="margin-top:20px;">
            <dt>User ID</dt><dd>#{{ $user->id }}</dd>
            <dt>Email</dt><dd>{{ $user->email }}</dd>
            <dt>Role</dt><dd>{{ ucfirst($user->role) }}</dd>
            @if ($user->created_at)
                <dt>Member since</dt><dd>{{ $user->created_at->format('M j, Y') }}</dd>
            @endif
        </dl>
        @if ($isAdmin)
            <a href="{{ route('cms.resource.edit', ['users', $user->id]) }}" class="sf-btn sf-btn-outline" style="margin-top:16px;">Edit account (Users)</a>
        @endif
    </div>

    <div class="sf-panel">
        <h2 style="margin:0 0 16px;font-size:1.05rem;font-weight:700;">Change Password</h2>
        <form method="POST" action="{{ route('cms.profile.password') }}" class="cms-form">
            @csrf
            @method('PUT')
            <label>
                Current password
                <input type="password" name="current_password" required autocomplete="current-password">
            </label>
            @error('current_password')
                <span class="cms-field-error">{{ $message }}</span>
            @enderror
            <label>
                New password
                <input type="password" name="password" required autocomplete="new-password" minlength="8">
            </label>
            <label>
                Confirm new password
                <input type="password" name="password_confirmation" required autocomplete="new-password">
            </label>
            @error('password')
                <span class="cms-field-error">{{ $message }}</span>
            @enderror
            <button type="submit" class="sf-btn sf-btn-primary">Update Password</button>
        </form>
    </div>
</div>
@endsection
