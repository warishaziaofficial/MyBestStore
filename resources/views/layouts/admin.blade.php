<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CMS') | MyBestStore</title>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}?v={{ filemtime(public_path('assets/css/app.css')) }}">
    <style>
        .cms-shell { min-height: 100vh; background: var(--secondary); }
        .cms-header { background: #fff; border-bottom: 1px solid var(--mbs-border); padding: 1rem 1.5rem; }
        .cms-header-inner { max-width: 72rem; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
        .cms-title { margin: 0; font-size: 1.125rem; font-weight: 700; color: var(--navy); }
        .cms-body { max-width: 72rem; margin: 0 auto; padding: 1.5rem; }
        .cms-card { background: #fff; border: 1px solid var(--mbs-border); border-radius: var(--radius-lg); padding: 1.25rem; }
        .cms-flash { margin-bottom: 1rem; padding: 0.75rem 1rem; border-radius: var(--radius-lg); font-size: 0.875rem; }
        .cms-flash--success { background: var(--accent-light); color: var(--mbs-green-dark); border: 1px solid color-mix(in srgb, var(--accent) 35%, var(--mbs-border)); }
        .cms-flash--error { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .cms-table { width: 100%; border-collapse: collapse; }
        .cms-table th, .cms-table td { padding: 0.75rem; border-bottom: 1px solid var(--mbs-border); text-align: left; font-size: 0.875rem; }
        .cms-table th { font-weight: 700; color: var(--navy); }
        .cms-link { color: var(--primary); font-weight: 600; text-decoration: none; }
        .cms-link:hover { color: var(--primary-hover); }
        .cms-field { margin-bottom: 1rem; }
        .cms-field label { display: block; margin-bottom: 0.375rem; font-size: 0.875rem; font-weight: 600; }
        .cms-multiselect { width: 100%; min-height: 10rem; padding: 0.5rem; border: 1px solid var(--mbs-border); border-radius: var(--radius-lg); font: inherit; }
        .cms-actions { display: flex; gap: 0.75rem; align-items: center; margin-top: 1.25rem; }
        .cms-search { display: flex; gap: 0.5rem; margin-bottom: 1rem; }
        .cms-search input { flex: 1; }
        .cms-nav { display: flex; gap: 1rem; flex-wrap: wrap; }
        .cms-nav a { color: var(--primary); font-weight: 600; text-decoration: none; font-size: 0.875rem; }
        .cms-nav a:hover { color: var(--primary-hover); }
        .cms-order-barcode { margin: 1rem 0; }
        .order-barcode-block { display: grid; gap: 0.5rem; }
        .order-barcode-grid { display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; }
        .order-barcode-image { max-width: 100%; height: auto; }
        .order-barcode-qr { width: 120px; height: 120px; }
        .order-barcode-code { margin: 0; font-family: ui-monospace, monospace; font-weight: 700; letter-spacing: 0.04em; }
        .cms-dispatch-scan-row { display: flex; gap: 0.75rem; }
        .cms-dispatch-input { flex: 1; font-family: ui-monospace, monospace; font-size: 1rem; }
        .cms-dispatch-label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
        .cms-order-meta p { margin: 0.35rem 0; }
    </style>
</head>
<body class="cms-shell font-sans text-foreground">
    <header class="cms-header">
        <div class="cms-header-inner">
            <div>
                <h1 class="cms-title">@yield('heading', 'CMS')</h1>
                <nav class="cms-nav" aria-label="CMS navigation">
                    <a href="{{ route('admin.products.index') }}">Products</a>
                    <a href="{{ route('admin.orders.index') }}">Orders</a>
                    <a href="{{ route('admin.orders.dispatch') }}">Dispatch</a>
                    <a href="{{ route('admin.couriers.index') }}">Couriers</a>
                    <a href="{{ route('admin.shipping.zones.index') }}">Shipping Zones</a>
                    <a href="{{ route('admin.shipping.rates.index') }}">Shipping Rates</a>
                </nav>
            </div>
            <a href="{{ route('home') }}" class="cms-link">Back to Store</a>
        </div>
    </header>
    <main class="cms-body">
        @if (session('success'))
            <div class="cms-flash cms-flash--success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="cms-flash cms-flash--error">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>
