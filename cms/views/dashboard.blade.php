@extends('cms::layouts.admin')

@section('title', 'Dashboard')

@section('page_heading')
<h1 class="sf-page-title">Dashboard</h1>
@endsection

@section('content')
@php
    $lowStock = (int) ($stockAlerts['low_stock'] ?? 0);
    $outOfStock = (int) ($stockAlerts['out_of_stock'] ?? 0);
@endphp
@if ($outOfStock > 0)
    <div class="sf-alert sf-alert-warn">
        <strong>Out of stock:</strong> {{ $outOfStock }} product{{ $outOfStock === 1 ? '' : 's' }}.
        @if ($lowStock > 0)
            {{ $lowStock }} also low on stock (no email for low stock).
        @endif
        <a href="{{ route('cms.reports') }}#inventory-alerts" class="sf-alert-link">View report →</a>
    </div>
@elseif ($lowStock > 0)
    <div class="sf-alert sf-alert-warn">
        <strong>Low stock:</strong> {{ $lowStock }} product{{ $lowStock === 1 ? '' : 's' }} at or below threshold (CMS display only — no email).
        <a href="{{ route('cms.reports') }}#inventory-alerts" class="sf-alert-link">View report →</a>
    </div>
@endif

<div class="sf-kpi-row">
    <div class="sf-kpi-card sf-kpi-card--blue">
        <div class="sf-kpi-icon sf-kpi-icon--blue">📦</div>
        <div>
            <p class="sf-kpi-label">Total Products</p>
            <p class="sf-kpi-value">{{ number_format($kpis['total_products'] ?? 0) }}</p>
        </div>
    </div>
    <div class="sf-kpi-card sf-kpi-card--pink">
        <div class="sf-kpi-icon sf-kpi-icon--pink">🛒</div>
        <div>
            <p class="sf-kpi-label">Total Orders</p>
            <p class="sf-kpi-value">{{ number_format($kpis['total_orders'] ?? 0) }}</p>
        </div>
    </div>
    <div class="sf-kpi-card sf-kpi-card--green">
        <div class="sf-kpi-icon sf-kpi-icon--green">📈</div>
        <div>
            <p class="sf-kpi-label">Total Revenue</p>
            <p class="sf-kpi-value">Rs {{ number_format($kpis['total_revenue'] ?? 0) }}</p>
        </div>
    </div>
    <div class="sf-kpi-card sf-kpi-card--orange">
        <div class="sf-kpi-icon sf-kpi-icon--orange">⏳</div>
        <div>
            <p class="sf-kpi-label">Pending Orders</p>
            <p class="sf-kpi-value">{{ number_format($kpis['pending_orders'] ?? 0) }}</p>
        </div>
    </div>
</div>

<div class="sf-panel sf-welcome">
    <img src="{{ $cmsLogoDarkUrl ?? asset('assets/cms/images/mybeststore-logo-dark.svg') }}" alt="DigitalWares" class="sf-welcome-logo">
    <h2>Welcome to DigitalWares Admin</h2>
    <p>This is your centralized admin dashboard. Here you can:</p>
    <ul>
        <li>Manage your product catalog — add, edit and delete products</li>
        <li>View and update orders with status tracking</li>
        <li>Monitor sales, revenue and inventory</li>
        <li>Manage homepage content, blog, FAQs and media</li>
        <li>Connect social accounts and sync orders from Instagram, TikTok, Facebook and WhatsApp</li>
    </ul>
</div>

<div class="sf-panel">
    <h2 style="margin:0 0 16px;font-size:1.1rem;font-weight:700;">Quick Access</h2>
    <div class="sf-quick-links">
        <a href="{{ route('cms.products.index') }}" class="sf-quick-link">Products</a>
        <a href="{{ route('cms.orders.index') }}" class="sf-quick-link">Orders</a>
        <a href="{{ route('cms.reports') }}" class="sf-quick-link">Reports</a>
        <a href="{{ route('cms.merchandising.featured') }}" class="sf-quick-link">Featured Products</a>
        <a href="{{ route('cms.merchandising.new-arrivals') }}" class="sf-quick-link">New Arrivals</a>
        <a href="{{ route('cms.resource.index', 'hero-slides') }}" class="sf-quick-link">Hero Slides</a>
        <a href="{{ route('cms.resource.index', 'blog-posts') }}" class="sf-quick-link">Blog</a>
        <a href="{{ route('cms.resource.index', 'media') }}" class="sf-quick-link">Media Library</a>
        <a href="{{ route('cms.resource.index', 'reviews') }}" class="sf-quick-link">Reviews</a>
        <a href="{{ route('cms.resource.index', 'inquiries') }}" class="sf-quick-link">Inquiries</a>
        <a href="{{ route('cms.settings.footer') }}" class="sf-quick-link">Footer Settings</a>
        <a href="{{ route('cms.social.index') }}" class="sf-quick-link">Social Integration</a>
    </div>
</div>
@endsection
