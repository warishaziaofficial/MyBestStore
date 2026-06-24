@extends('cms::layouts.admin')

@section('title', 'Dashboard')

@section('page_heading')
<div class="sf-page-banner__title-block">
    <h1 class="sf-page-title">Dashboard</h1>
    <p class="sf-page-subtitle">Overview of catalog, orders, revenue and quick shortcuts.</p>
</div>
@endsection

@section('page_banner_extra')
@include('cms::dashboard._shortcuts')
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
@endsection
