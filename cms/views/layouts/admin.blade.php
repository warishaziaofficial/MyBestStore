<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CMS') | MyBestStore</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/cms/css/admin.css') }}?v={{ @filemtime(public_path('assets/cms/css/admin.css')) ?: 1 }}">
</head>
<body class="cms-body">
    @php
        $cmsLogoPath = config('cms.logo', 'logo.png');
        $cmsLogoUrl = is_file(public_path($cmsLogoPath))
            ? asset($cmsLogoPath)
            : asset(config('cms.logo_fallback', 'assets/cms/images/mybeststore-logo.svg'));
        $cmsLogoDarkUrl = is_file(public_path($cmsLogoPath))
            ? asset($cmsLogoPath)
            : asset(config('cms.logo_fallback_dark', 'assets/cms/images/mybeststore-logo-dark.svg'));
    @endphp
    @authCms
        <div class="sf-shell">
            <aside class="sf-sidebar">
                <a href="{{ route('cms.dashboard') }}" class="sf-brand">
                    <img
                        src="{{ $cmsLogoUrl }}"
                        alt="MyBestStore"
                        class="sf-brand-logo @if(is_file(public_path($cmsLogoPath))) sf-brand-logo--invert @endif"
                    >
                </a>

                <nav class="sf-nav">
                    <a href="{{ route('cms.dashboard') }}" @class(['sf-nav-link', 'is-active' => request()->routeIs('cms.dashboard')])>
                        <span class="sf-nav-icon">▦</span> Dashboard
                    </a>
                    <a href="{{ route('cms.reports') }}" @class(['sf-nav-link', 'is-active' => request()->routeIs('cms.reports')])>
                        <span class="sf-nav-icon">📊</span> Reports &amp; Analytics
                    </a>

                    <p class="sf-nav-section">Catalog</p>
                    <a href="{{ route('cms.products.index') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->routeIs('cms.products.*') || request()->is('cms/manage/products*')])>Products</a>
                    <a href="{{ route('cms.resource.index', 'product-images') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/product-images*')])>Product Gallery</a>
                    <a href="{{ route('cms.resource.index', 'categories') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/categories*')])>Categories</a>
                    <a href="{{ route('cms.resource.index', 'brands') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/brands*')])>Brands</a>
                    <a href="{{ route('cms.merchandising.featured') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->routeIs('cms.merchandising.featured')])>Featured Products</a>
                    <a href="{{ route('cms.merchandising.new-arrivals') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->routeIs('cms.merchandising.new-arrivals')])>New Arrivals</a>

                    <p class="sf-nav-section">Commerce</p>
                    <a href="{{ route('cms.orders.index') }}" @class(['sf-nav-link', 'is-active' => request()->routeIs('cms.orders.*') && ! request()->is('cms/manage/orders*') && ! request()->routeIs('cms.dispatch.*')])>
                        <span class="sf-nav-icon">🛒</span> Orders
                    </a>
                    <a href="{{ route('cms.dispatch.queue') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->routeIs('cms.dispatch.*')])>Dispatch Queue</a>
                    <a href="{{ route('cms.resource.index', 'order-items') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/order-items*')])>Order Items</a>
                    <a href="{{ route('cms.resource.index', 'refunds') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/refunds*')])>Refunds</a>

                    <p class="sf-nav-section">Reviews</p>
                    <a href="{{ route('cms.resource.index', 'reviews') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/reviews*')])>Reviews</a>
                    <a href="{{ route('cms.resource.index', 'ratings') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/ratings*')])>Ratings</a>
                    <a href="{{ route('cms.resource.index', 'testimonials') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/testimonials*')])>Testimonials</a>

                    <p class="sf-nav-section">Website Content</p>
                    <a href="{{ route('cms.resource.index', 'hero-slides') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/hero-slides*')])>Hero Slides</a>
                    <a href="{{ route('cms.resource.index', 'promo-banners') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/promo-banners*')])>Promo Banners</a>
                    <a href="{{ route('cms.resource.index', 'featured-collections') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/featured-collections*')])>Featured Collections</a>
                    <a href="{{ route('cms.resource.index', 'trust-items') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/trust-items*')])>Trust Bar</a>
                    <a href="{{ route('cms.resource.index', 'faqs') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/faqs*')])>FAQs</a>
                    <a href="{{ route('cms.resource.index', 'blog-posts') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/blog-posts*')])>Blog Posts</a>
                    <a href="{{ route('cms.resource.index', 'blog-categories') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/blog-categories*')])>Blog Categories</a>
                    <a href="{{ route('cms.resource.index', 'blog-tags') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/blog-tags*')])>Blog Tags</a>
                    <a href="{{ route('cms.resource.index', 'static-pages') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/static-pages*')])>Static Pages</a>
                    <a href="{{ route('cms.resource.index', 'contact-cards') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/contact-cards*')])>Contact Cards</a>
                    <a href="{{ route('cms.resource.index', 'media') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/media*')])>Media Library</a>

                    <p class="sf-nav-section">People &amp; Leads</p>
                    <a href="{{ route('cms.resource.index', 'customers') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/customers*')])>Customers</a>
                    <a href="{{ route('cms.resource.index', 'inquiries') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->routeIs('cms.inquiries.*') || request()->is('cms/manage/inquiries*')])>Inquiries</a>
                    <a href="{{ route('cms.resource.index', 'newsletter-subscribers') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/newsletter-subscribers*')])>Newsletter</a>
                    @if ($canEdit ?? false)
                        <a href="{{ route('cms.customers.password-reset') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->routeIs('cms.customers.password-reset*')])>Customer Password Reset</a>
                    @endif

                    <p class="sf-nav-section">Integrations</p>
                    <a href="{{ route('cms.social.index') }}" @class(['sf-nav-link', 'is-active' => request()->routeIs('cms.social.*') || request()->is('cms/manage/social-accounts*') || request()->is('cms/manage/social-sync-logs*')])>
                        <span class="sf-nav-icon">🔗</span> Social Integration
                    </a>
                    <a href="{{ route('cms.resource.index', 'email-templates') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/email-templates*')])>Email Templates</a>

                    <p class="sf-nav-section">Settings</p>
                    @if ($isAdmin ?? false)
                        <a href="{{ route('cms.resource.index', 'users') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/users*')])>Users &amp; Roles</a>
                    @endif
                    <a href="{{ route('cms.settings.footer') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->routeIs('cms.settings.footer*')])>Footer Settings</a>
                    <a href="{{ route('home') }}" target="_blank" rel="noopener" class="sf-nav-link sf-nav-link--sub">View Storefront ↗</a>
                </nav>

                <form method="POST" action="{{ route('cms.logout') }}" class="sf-logout">
                    @csrf
                    <button type="submit" class="sf-nav-link sf-nav-link--logout">↪ Logout</button>
                </form>
            </aside>

            <main class="sf-main">
                <div class="sf-main-header">
                    <div class="sf-main-header__page">
                        @yield('page_heading')
                    </div>
                    <div class="sf-topbar">
                    <details class="sf-profile-wrap">
                        <summary class="sf-profile-btn">
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
                    </div>
                </div>
                <div id="sf-toast-stack" class="sf-toast-stack" aria-live="polite" aria-atomic="false"></div>
                @if (session('success'))
                    <div class="sf-alert sf-alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="sf-alert sf-alert-error">{{ session('error') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    @else
        @yield('content')
    @endauthCms
    @authCms
        @php
            $latestNotificationId = ($recentNotifications ?? collect())->max('id') ?? 0;
        @endphp
        <script>
            window.cmsNotifications = {
                pollUrl: @json(route('cms.notifications.poll')),
                indexUrl: @json(route('cms.notifications.index')),
                sinceId: {{ (int) $latestNotificationId }},
                favicon: @json(asset('favicon.ico')),
            };
        </script>
        <script src="{{ asset('assets/cms/js/notifications.js') }}?v={{ @filemtime(public_path('assets/cms/js/notifications.js')) ?: 1 }}" defer></script>
    @endauthCms
</body>
</html>
