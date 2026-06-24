<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CMS') | DigitalWares</title>
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
                        alt="DigitalWares"
                        class="sf-brand-logo @if(is_file(public_path($cmsLogoPath))) sf-brand-logo--invert @endif"
                    >
                </a>

                @php
                    $navCatalogOpen = request()->routeIs('cms.products.*')
                        || request()->is('cms/manage/product*')
                        || request()->is('cms/manage/categories*')
                        || request()->is('cms/manage/brands*')
                        || request()->routeIs('cms.merchandising.*');
                    $navCommerceOpen = request()->routeIs('cms.orders.*')
                        || request()->routeIs('cms.dispatch.*')
                        || request()->is('cms/manage/order-items*')
                        || request()->is('cms/manage/refunds*');
                    $navReviewsOpen = request()->is('cms/manage/reviews*')
                        || request()->is('cms/manage/ratings*')
                        || request()->is('cms/manage/testimonials*');
                    $navContentOpen = request()->is('cms/manage/hero-slides*')
                        || request()->is('cms/manage/promo-banners*')
                        || request()->is('cms/manage/featured-collections*')
                        || request()->is('cms/manage/trust-items*')
                        || request()->is('cms/manage/faqs*')
                        || request()->is('cms/manage/blog-*')
                        || request()->is('cms/manage/static-pages*')
                        || request()->is('cms/manage/contact-cards*')
                        || request()->is('cms/manage/media*');
                    $navPeopleOpen = request()->is('cms/manage/customers*')
                        || request()->routeIs('cms.inquiries.*')
                        || request()->is('cms/manage/inquiries*')
                        || request()->is('cms/manage/newsletter-subscribers*')
                        || request()->routeIs('cms.customers.password-reset*');
                    $navIntegrationsOpen = request()->routeIs('cms.social.*')
                        || request()->is('cms/manage/social-*')
                        || request()->is('cms/manage/email-templates*');
                    $navSettingsOpen = request()->is('cms/manage/users*')
                        || request()->routeIs('cms.settings.footer*');
                @endphp
                <nav class="sf-nav">
                    <a href="{{ route('cms.dashboard') }}" @class(['sf-nav-link', 'is-active' => request()->routeIs('cms.dashboard')])>
                        <span class="sf-nav-icon">▦</span> Dashboard
                    </a>
                    <a href="{{ route('cms.reports') }}" @class(['sf-nav-link', 'is-active' => request()->routeIs('cms.reports')])>
                        <span class="sf-nav-icon">📊</span> Reports
                    </a>

                    <details class="sf-nav-group" @if($navCatalogOpen) open @endif>
                        <summary class="sf-nav-group-title">Catalog</summary>
                        <div class="sf-nav-group-links">
                            <a href="{{ route('cms.products.index') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->routeIs('cms.products.*') || request()->is('cms/manage/products*')])>Products</a>
                            <a href="{{ route('cms.resource.index', 'product-images') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/product-images*')])>Product Gallery</a>
                            <a href="{{ route('cms.resource.index', 'categories') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/categories*')])>Categories</a>
                            <a href="{{ route('cms.resource.index', 'brands') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/brands*')])>Brands</a>
                            <a href="{{ route('cms.merchandising.featured') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->routeIs('cms.merchandising.featured')])>Featured Products</a>
                            <a href="{{ route('cms.merchandising.new-arrivals') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->routeIs('cms.merchandising.new-arrivals')])>New Arrivals</a>
                        </div>
                    </details>

                    <details class="sf-nav-group" @if($navCommerceOpen) open @endif>
                        <summary class="sf-nav-group-title">Commerce</summary>
                        <div class="sf-nav-group-links">
                            <a href="{{ route('cms.orders.index') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->routeIs('cms.orders.*') && ! request()->is('cms/manage/orders*') && ! request()->routeIs('cms.dispatch.*')])>Orders</a>
                            <a href="{{ route('cms.dispatch.queue') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->routeIs('cms.dispatch.*')])>Dispatch Queue</a>
                            <a href="{{ route('cms.resource.index', 'order-items') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/order-items*')])>Order Items</a>
                            <a href="{{ route('cms.resource.index', 'refunds') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/refunds*')])>Refunds</a>
                        </div>
                    </details>

                    <details class="sf-nav-group" @if($navReviewsOpen) open @endif>
                        <summary class="sf-nav-group-title">Reviews</summary>
                        <div class="sf-nav-group-links">
                            <a href="{{ route('cms.resource.index', 'reviews') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/reviews*')])>Reviews</a>
                            <a href="{{ route('cms.resource.index', 'ratings') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/ratings*')])>Ratings</a>
                            <a href="{{ route('cms.resource.index', 'testimonials') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/testimonials*')])>Testimonials</a>
                        </div>
                    </details>

                    <details class="sf-nav-group" @if($navContentOpen) open @endif>
                        <summary class="sf-nav-group-title">Website Content</summary>
                        <div class="sf-nav-group-links">
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
                        </div>
                    </details>

                    <details class="sf-nav-group" @if($navPeopleOpen) open @endif>
                        <summary class="sf-nav-group-title">People &amp; Leads</summary>
                        <div class="sf-nav-group-links">
                            <a href="{{ route('cms.resource.index', 'customers') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/customers*')])>Customers</a>
                            <a href="{{ route('cms.resource.index', 'inquiries') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->routeIs('cms.inquiries.*') || request()->is('cms/manage/inquiries*')])>Inquiries</a>
                            <a href="{{ route('cms.resource.index', 'newsletter-subscribers') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/newsletter-subscribers*')])>Newsletter</a>
                            @if ($canEdit ?? false)
                                <a href="{{ route('cms.customers.password-reset') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->routeIs('cms.customers.password-reset*')])>Password Reset</a>
                            @endif
                        </div>
                    </details>

                    <details class="sf-nav-group" @if($navIntegrationsOpen) open @endif>
                        <summary class="sf-nav-group-title">Integrations</summary>
                        <div class="sf-nav-group-links">
                            <a href="{{ route('cms.social.index') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->routeIs('cms.social.*') || request()->is('cms/manage/social-accounts*') || request()->is('cms/manage/social-sync-logs*')])>Social Integration</a>
                            <a href="{{ route('cms.resource.index', 'email-templates') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/email-templates*')])>Email Templates</a>
                        </div>
                    </details>

                    <details class="sf-nav-group" @if($navSettingsOpen) open @endif>
                        <summary class="sf-nav-group-title">Settings</summary>
                        <div class="sf-nav-group-links">
                            @if ($isAdmin ?? false)
                                <a href="{{ route('cms.resource.index', 'users') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->is('cms/manage/users*')])>Users &amp; Roles</a>
                            @endif
                            <a href="{{ route('cms.settings.footer') }}" @class(['sf-nav-link sf-nav-link--sub', 'is-active' => request()->routeIs('cms.settings.footer*')])>Footer Settings</a>
                            <a href="{{ route('home') }}" target="_blank" rel="noopener" class="sf-nav-link sf-nav-link--sub">View Storefront ↗</a>
                        </div>
                    </details>
                </nav>

                <form method="POST" action="{{ route('cms.logout') }}" class="sf-logout">
                    @csrf
                    <button type="submit" class="sf-nav-link sf-nav-link--logout">↪ Logout</button>
                </form>
            </aside>

            <main class="sf-main">
                <header class="sf-page-banner">
                    <div class="sf-page-banner__row">
                        <div class="sf-page-banner__heading">
                            @yield('page_heading')
                        </div>
                        <div class="sf-page-banner__tools">
                            @hasSection('page_actions')
                                <div class="sf-page-banner__actions">
                                    @yield('page_actions')
                                </div>
                            @endif
                            <div class="sf-page-banner__account">
                                @include('cms::layouts._banner-toolbar')
                            </div>
                        </div>
                    </div>
                    @hasSection('page_banner_extra')
                        <div class="sf-page-banner__extra">
                            @yield('page_banner_extra')
                        </div>
                    @endif
                </header>
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
