<details class="sf-profile-wrap">
    <summary class="sf-profile-btn" title="Account menu">
        <span class="sf-profile-avatar">{{ strtoupper(substr($cmsUserName ?? 'A', 0, 1)) }}</span>
        <span class="sf-topbar-user">
            <span class="sf-topbar-name">{{ $cmsUserName ?? 'Admin' }}</span>
            <span class="sf-topbar-role">{{ ucfirst($cmsUserRole ?? 'admin') }}</span>
        </span>
        <span class="sf-profile-caret">▾</span>
    </summary>
    <div class="sf-profile-dropdown">
        <div class="sf-profile-dropdown-head">
            <strong>{{ $cmsUserName ?? 'Admin' }}</strong>
            <span>{{ $cmsUserEmail ?? '' }}</span>
            <span class="sf-pill sf-pill--blue">{{ ucfirst($cmsUserRole ?? 'admin') }}</span>
        </div>
        <a href="{{ route('cms.profile') }}" class="sf-profile-link">👤 My Profile</a>
        <a href="{{ route('cms.dashboard') }}" class="sf-profile-link">▦ Dashboard</a>
        @if ($isAdmin ?? false)
            <a href="{{ route('cms.resource.index', 'users') }}" class="sf-profile-link">🔐 Users &amp; Roles</a>
        @endif
        <form method="POST" action="{{ route('cms.logout') }}" class="sf-profile-logout">
            @csrf
            <button type="submit" class="sf-profile-link sf-profile-link--logout">↪ Sign Out</button>
        </form>
    </div>
</details>
<details class="sf-notify-wrap">
    <summary class="sf-notify-btn" title="Notifications">
        🔔
        <span
            id="sf-notify-badge"
            class="sf-notify-badge @if (($notificationCount ?? 0) === 0) is-empty @endif"
            aria-live="polite"
        >{{ ($notificationCount ?? 0) > 99 ? '99+' : ($notificationCount ?? 0) }}</span>
    </summary>
    <div class="sf-notify-dropdown">
        <div id="sf-notify-list">
        @forelse ($recentNotifications as $notification)
            <a href="{{ $notification->link ?: route('cms.notifications.index') }}" class="sf-notify-item {{ $notification->is_read ? '' : 'is-unread' }}">
                <strong>{{ $notification->title }}</strong>
                <span>{{ \Illuminate\Support\Str::limit($notification->body, 60) }}</span>
            </a>
        @empty
            <p class="sf-notify-empty">No notifications yet.</p>
        @endforelse
        </div>
        <a href="{{ route('cms.notifications.index') }}" class="sf-notify-footer">View all notifications</a>
    </div>
</details>
