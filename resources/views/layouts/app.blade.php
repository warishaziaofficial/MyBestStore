<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('storefront.store_name', 'DigitalWares').' | Premium Digital Hardware')</title>
    <meta name="description" content="@yield('meta_description', config('storefront.store_name', 'DigitalWares').' — POS systems, barcode scanners, biometric devices and digital hardware in Pakistan.')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        background: '#ffffff',
                        foreground: '#111827',
                        primary: {
                            DEFAULT: '#F97316',
                            hover: '#EA580C',
                            light: '#FFF7ED',
                        },
                        accent: {
                            DEFAULT: '#F97316',
                            light: '#FFF7ED',
                        },
                        secondary: '#FFFBF7',
                        card: '#ffffff',
                        muted: '#64748B',
                        border: '#FED7AA',
                        navy: '#0F172A',
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    },
                    maxWidth: {
                        '8xl': '80rem',
                    },
                }
            }
        }
    </script>
    <script>
        window.__MBS__ = {
            cartCount: {{ $cartCount ?? 0 }},
            wishlistSlugs: @json($wishlistSlugs ?? []),
            compareSlugs: @json($compareSlugs ?? []),
            openCart: {{ session('open_cart') ? 'true' : 'false' }},
            customer: @json($authCustomer ?? null),
            authUrls: {
                login: @json(route('customer.login')),
                register: @json(route('customer.register')),
                forgotPassword: @json(route('customer.forgot-password')),
                trackOrder: @json(route('customer.track-order')),
                logout: @json(route('customer.logout')),
            },
        };
    </script>
    <script src="{{ asset('assets/js/mbs-storefront.js') }}?v={{ filemtime(public_path('assets/js/mbs-storefront.js')) }}"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}?v={{ filemtime(public_path('assets/css/app.css')) }}">
</head>
<body class="min-h-full bg-background font-sans text-foreground antialiased" x-data="mbsStorefront()">
    <div
        x-show="toastVisible"
        x-cloak
        x-transition
        class="mbs-toast"
        role="status"
        aria-live="polite"
        x-text="toastMessage"
    ></div>
    @if (session('success'))
        <div class="mbs-flash mbs-flash--success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mbs-flash mbs-flash--error">{{ session('error') }}</div>
    @endif

    @include('components.header')

    <main class="flex-1">
        @yield('content')
    </main>

    @include('components.footer')

    @include('components.search-modal')
    @include('components.sign-in-modal')
    @include('components.cart-drawer')
    @include('components.quick-view-modal')
    @include('components.newsletter-popup')
    @include('components.chat-widget')
</body>
</html>
