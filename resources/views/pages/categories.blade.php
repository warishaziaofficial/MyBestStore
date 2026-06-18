@extends('layouts.app')
@php use App\Support\Mbs; @endphp

@section('title', 'MyBestStore | Categories')

@section('content')
@include('components.page-hero', [
    'title' => 'Shop By Category',
    'description' => 'Browse electronics, audio, appliances, books & media and more.',
])

<section class="bg-white py-10">
    <div class="mbs-container">
        <div class="mbs-shop-category-grid">
            @forelse ($categories as $category)
                @include('components.shop-category-tile', ['category' => $category])
            @empty
                <p class="col-span-full rounded-2xl border border-border bg-white p-8 text-center text-muted">No categories found.</p>
            @endforelse
        </div>
    </div>
</section>

<section class="bg-secondary py-10">
    <div class="mbs-container">
        @include('components.section-header', ['title' => 'Featured Category Banners'])
        <div class="home-section-inner grid gap-5 md:grid-cols-3">
            @foreach ($categoryBanners as $banner)
                <a href="{{ route($banner['href']) }}" class="group relative overflow-hidden rounded-2xl border border-border">
                    <img src="{{ Mbs::image($banner['image']) }}" alt="{{ $banner['title'] }}" class="h-48 w-full object-cover transition duration-500 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-navy/80 to-transparent"></div>
                    <h3 class="absolute bottom-4 left-4 text-xl font-bold text-white">{{ $banner['title'] }}</h3>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white py-10">
    <div class="mbs-container">
        @include('components.section-header', ['title' => 'Popular Products', 'viewAllHref' => route('shop')])
        <div class="home-section-inner grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($popularProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endsection
