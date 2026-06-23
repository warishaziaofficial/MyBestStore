@extends('cms::layouts.admin')

@section('title', 'Notifications')

@section('page_heading')
<div class="sf-page-head">
    <div>
        <h1 class="sf-page-title">🔔 Notifications</h1>
        <p class="sf-page-subtitle">In-app alerts for orders, stock and inquiries. SMS sent when configured.</p>
    </div>
    @if ($unreadCount > 0)
        <form method="POST" action="{{ route('cms.notifications.read-all') }}">
            @csrf
            <button type="submit" class="sf-btn sf-btn-outline">Mark all read</button>
        </form>
    @endif
</div>
@endsection

@section('content')
<div class="sf-panel">
    @forelse ($notifications as $notification)
        <div @class(['sf-notify-row', 'is-unread' => ! $notification->is_read])>
            <div>
                <strong>{{ $notification->title }}</strong>
                <p class="cms-muted" style="margin:4px 0 0;">{{ $notification->body }}</p>
                <span class="sf-subtext">{{ $notification->created_at?->diffForHumans() }} · {{ strtoupper($notification->type) }}</span>
            </div>
            <div class="sf-actions">
                @if ($notification->link)
                    <a href="{{ $notification->link }}" class="sf-action sf-action--edit">Open</a>
                @endif
                @if (! $notification->is_read)
                    <form method="POST" action="{{ route('cms.notifications.read', $notification->id) }}">
                        @csrf
                        <button type="submit" class="sf-action">Mark read</button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <p class="sf-empty">No notifications yet. You'll see alerts here for new orders, out-of-stock products and contact inquiries.</p>
    @endforelse
</div>
@endsection
