<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MyBestStore | Premium Electronics')</title>
    <meta name="description" content="@yield('meta_description', 'MyBestStore — premium electronics and home entertainment in Pakistan.')">
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
                        foreground: '#1e293b',
                        primary: {
                            DEFAULT: '#1b4f9b',
                            hover: '#164080',
                            light: '#e8f0fb',
                        },
                        accent: {
                            DEFAULT: '#2eaf5e',
                            light: '#e8f5ee',
                        },
                        secondary: '#f8fafc',
                        card: '#ffffff',
                        muted: '#64748b',
                        border: '#e2e8f0',
                        navy: '#0f2744',
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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}?v={{ filemtime(public_path('assets/css/app.css')) }}">
</head>
<body
    class="min-h-full bg-background font-sans text-foreground antialiased"
    x-data="{
        searchOpen: false,
        loginOpen: false,
        cartOpen: false,
        quickViewOpen: false,
        quickViewTitle: '',
        quickViewImage: '',
        filterOpen: false,
        mobileNavOpen: false,
        openDropdown: null
    }"
>
    @include('components.header')

    <main class="flex-1">
        @yield('content')
    </main>

    @include('components.footer')

    @include('components.search-modal')
    @include('components.sign-in-modal')
    @include('components.cart-drawer')
    @include('components.quick-view-modal')
</body>
</html>
