@extends('layouts.app')
@php use App\Support\Mbs; @endphp

@section('title', 'DigitalWares | Categories')

@section('content')
@include('components.page-hero', [
    'title' => $categoryMeta['name'] ?? 'Shop By Brands',
    'description' => $categoryMeta['description'] ?? 'Browse electronics, audio, appliances, books & media and more.',
])

@include('components.shop-catalog', [
    'products' => $products,
    'filters' => $filters,
    'catalogAction' => $catalogAction,
    'catalogContext' => $catalogContext,
    'sortOptions' => $sortOptions,
    'categoryCounts' => $categoryCounts,
    'clearFiltersUrl' => $clearFiltersUrl,
    'showNewArrivalsFilter' => $showNewArrivalsFilter ?? false,
    'showFeaturedFilter' => $showFeaturedFilter ?? true,
    'label' => 'products',
])

<section class="bg-white py-10">
    <div class="mbs-container">
        @include('components.section-header', [
            'title' => 'Browse Categories',
            'subtitle' => 'Find the right collection for your next purchase',
        ])
        <div class="mbs-shop-category-grid">
            @forelse ($categories as $category)
                @include('components.shop-category-tile', ['category' => $category])
            @empty
                <p class="col-span-full rounded-2xl border border-border bg-white p-8 text-center text-muted">No categories found.</p>
            @endforelse
        </div>
    </div>
</section>

<section class="mbs-category-banners-section">
    <div class="mbs-container">
        @include('components.section-header', [
            'title' => 'Featured Category Banners',
            'subtitle' => 'Explore POS, barcode, security and access control collections',
            'viewAllHref' => route('shop'),
        ])
        <div class="mbs-category-banner-grid">
            @foreach ($categoryBanners as $banner)
                <a
                    href="{{ !empty($banner['slug']) ? Mbs::shopCategoryUrl($banner['slug']) : route($banner['href'] ?? 'shop') }}"
                    class="mbs-category-banner-card"
                >
                    <div class="mbs-category-banner-media">
                        <img
                            src="{{ Mbs::image($banner['image']) }}"
                            alt="{{ $banner['title'] }}"
                            class="mbs-category-banner-image"
                            loading="lazy"
                        >
                    </div>
                    <div class="mbs-category-banner-body">
                        <h3 class="mbs-category-banner-title">{{ $banner['title'] }}</h3>
                        @if (!empty($banner['subtitle']))
                            <p class="mbs-category-banner-subtitle">{{ $banner['subtitle'] }}</p>
                        @endif
                        <span class="mbs-category-banner-cta">Shop Now <span aria-hidden="true">→</span></span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endsection
