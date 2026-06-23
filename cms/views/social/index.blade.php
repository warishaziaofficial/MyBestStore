@extends('cms::layouts.admin')

@section('title', 'Social Integration')

@section('page_heading')
<h1 class="sf-page-title">Social Integration</h1>
@endsection

@section('content')
<div class="sf-info-banner">
    Connect your social media accounts to sync orders directly from Instagram, TikTok, Facebook, and WhatsApp.
    The platform will fetch orders and automatically create them in your system.
</div>

@if ($accounts->isEmpty())
    <div class="sf-panel sf-empty">
        <p>No social accounts connected yet.</p>
        @if ($canEdit)
            <a href="{{ route('cms.resource.create', 'social-accounts') }}" class="sf-btn sf-btn-primary">+ Add Social Account</a>
        @endif
    </div>
@else
    <div class="sf-social-grid">
        @foreach ($accounts as $account)
            <div class="sf-social-card">
                <div class="sf-social-card-head">
                    <h3>{{ strtoupper($account->platform) }}</h3>
                    <span @class(['sf-dot', 'sf-dot--on' => $account->status === 'connected', 'sf-dot--off' => $account->status !== 'connected'])></span>
                </div>
                <dl class="sf-social-meta">
                    <div><dt>Account Name</dt><dd>{{ $account->account_name }}</dd></div>
                    <div><dt>Account ID</dt><dd>{{ $account->account_id }}</dd></div>
                    <div><dt>Orders Synced</dt><dd>{{ $account->orders_synced_count ?? 0 }}</dd></div>
                    <div><dt>Last Sync</dt><dd>{{ $account->last_sync_at?->format('n/j/Y, g:i:s A') ?? 'Never' }}</dd></div>
                </dl>
                @if ($canEdit)
                    <form method="POST" action="{{ route('cms.social.sync', $account->id) }}">
                        @csrf
                        <button type="submit" class="sf-btn sf-btn-primary sf-btn-block">↻ Sync Orders</button>
                    </form>
                    <a href="{{ route('cms.resource.edit', ['social-accounts', $account->id]) }}" class="sf-link-muted">Edit account</a>
                @endif
            </div>
        @endforeach
    </div>
@endif

<div class="sf-panel sf-webhook-panel">
    <h2>Webhook Testing</h2>
    <p class="sf-muted">Test webhook integration by sending a simulated order from social platforms.</p>
    @if ($canEdit)
        <form method="POST" action="{{ route('cms.social.test-webhook') }}" class="sf-webhook-form">
            @csrf
            <select name="platform">
                @foreach ($platforms as $platform)
                    <option value="{{ $platform }}">{{ ucfirst($platform) }}</option>
                @endforeach
            </select>
            <button type="submit" class="sf-btn sf-btn-outline">Send Test Webhook</button>
        </form>
    @endif
</div>
@endsection
