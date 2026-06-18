@extends('layouts.app')
@php use App\Support\Mbs; @endphp

@section('title', 'MyBestStore | Premium Electronics')

@section('content')
@include('components.hero-slider', ['slides' => $heroSlides])
@include('components.category-strip')

{{-- Special Offers --}}
<section class="home-section mbs-promo-section bg-white">
    <div class="mbs-container">
        @include('components.section-header', ['title' => 'Special Offers', 'subtitle' => 'Limited-time deals on top electronics'])
        <div class="home-section-inner mbs-promo-banners">
            <a href="{{ route('shop') }}" class="mbs-promo-banner">
                <img src="{{ asset('banners/home-entertainment.jpg') }}" alt="Built-in Voice Assistants" class="mbs-promo-banner-bg">
                <div class="mbs-promo-banner-overlay"></div>
                <div class="mbs-promo-banner-content">
                    <span class="mbs-promo-banner-label">SMART Series</span>
                    <h3 class="mbs-promo-banner-title">Built-in Voice Assistants</h3>
                    <span class="mbs-promo-banner-btn">Shop Now</span>
                </div>
            </a>
            <a href="{{ route('shop') }}" class="mbs-promo-banner">
                <img src="{{ asset('banners/audio-collection.jpg') }}" alt="Advanced Technology" class="mbs-promo-banner-bg">
                <div class="mbs-promo-banner-overlay"></div>
                <div class="mbs-promo-banner-content">
                    <span class="mbs-promo-banner-label">High-Resolution</span>
                    <h3 class="mbs-promo-banner-title">Advanced Technology</h3>
                    <span class="mbs-promo-banner-btn">Shop Now</span>
                </div>
            </a>
        </div>
    </div>
</section>

{{-- Shop By Category --}}
@php
    $shopCategories = [
        ['name' => 'Ear Buds', 'image' => 'images/categories/mobile-accessories.jpg'],
        ['name' => 'Head Phone', 'image' => 'images/categories/audio-equipment.jpg'],
        ['name' => 'Sound Bar Speakers', 'image' => 'images/categories/sound-bars.jpg'],
        ['name' => 'Amplifier Woofer', 'image' => 'products/showcase-soundbar.jpg'],
        ['name' => 'LED TVs', 'image' => 'images/categories/led-tvs.jpg'],
        ['name' => 'Air Purifiers', 'image' => 'images/categories/air-purifiers.jpg'],
        ['name' => 'Home Theater', 'image' => 'images/categories/home-theater.jpg'],
        ['name' => 'Accessories', 'image' => 'images/categories/accessories.jpg'],
    ];
@endphp
<section class="home-section mbs-shop-category-section">
    <div class="mbs-container">
        @include('components.section-header', ['title' => 'Shop By Category', 'subtitle' => 'Browse our most popular collections', 'viewAllHref' => route('categories')])
        <div class="home-section-inner mbs-shop-category-grid">
            @foreach ($shopCategories as $category)
                @include('components.shop-category-tile', ['category' => $category])
            @endforeach
        </div>
    </div>
</section>

@include('components.product-grid-section', ['title' => 'Best Selling Products', 'subtitle' => 'Top picks loved by customers', 'products' => array_slice($bestSelling, 0, 4), 'viewAllHref' => route('shop'), 'bg' => 'bg-white'])
@include('components.product-grid-section', ['title' => 'New Arrivals', 'subtitle' => 'Recently added to MyBestStore', 'products' => array_slice($newArrivals, 0, 6), 'viewAllHref' => route('new-arrivals'), 'bg' => 'bg-secondary', 'grid' => '3'])
@include('components.product-grid-section', ['title' => 'LED TVs', 'products' => $ledTvs, 'viewAllHref' => route('shop'), 'bg' => 'bg-white'])
@include('components.product-grid-section', ['title' => 'Sound Bars', 'products' => $soundBars, 'viewAllHref' => route('shop'), 'bg' => 'bg-secondary'])
@include('components.product-grid-section', ['title' => 'Air Purifiers', 'products' => $airPurifiers, 'viewAllHref' => route('shop'), 'bg' => 'bg-white'])
@include('components.product-grid-section', ['title' => 'Home Theater', 'products' => $homeTheater, 'viewAllHref' => route('shop'), 'bg' => 'bg-secondary'])

{{-- Exclusive Deals --}}
<section id="deals" class="home-section bg-white">
    <div class="mbs-container">
        @include('components.section-header', ['title' => 'Exclusive Electronics Deals', 'subtitle' => 'Hand-picked offers you cannot miss'])
        <div class="home-section-inner grid gap-6 lg:grid-cols-[35%_1fr]">
            <a href="{{ route('shop') }}" class="mbs-deals-banner group">
                <img src="{{ Mbs::image('banners/featured-deal.jpg') }}" alt="Featured deal">
                <div class="absolute inset-0 bg-navy/50"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                    <p class="text-xs font-bold uppercase tracking-widest text-blue-200">Featured Deal</p>
                    <h3 class="mt-2 text-2xl font-bold">Save Big On Premium Audio</h3>
                </div>
            </a>
            <div class="grid gap-5 sm:grid-cols-2">
                @foreach (array_slice($dealProducts, 0, 4) as $product)
                    @include('components.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Trust bar --}}
<section class="home-section mbs-trust-section">
    <div class="mbs-container">
        @include('components.section-header', ['title' => 'Why Shop With MyBestStore', 'subtitle' => 'Trusted by customers across Pakistan', 'centered' => true])
        <div class="home-section-inner grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($trustItems as $item)
                <div class="mbs-trust-card">
                    <div class="mbs-trust-icon">{{ $item['icon'] }}</div>
                    <h3 class="mt-4 font-bold text-navy">{{ $item['title'] }}</h3>
                    <p class="mt-2 text-sm text-muted">{{ $item['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Featured product showcase --}}
@if ($showcaseProduct)
<section class="home-section bg-white">
    <div class="mbs-container">
        @include('components.section-header', ['title' => 'Featured Product', 'subtitle' => "Editor's pick this week"])
        <div class="home-section-inner grid gap-8 lg:grid-cols-2">
            <div class="grid grid-cols-2 gap-3">
                @foreach ($showcaseGallery as $img)
                    <div class="overflow-hidden rounded-2xl border border-border bg-secondary {{ $loop->first ? 'col-span-2 h-64' : 'h-40' }}">
                        <img src="{{ Mbs::image($img) }}" alt="" class="h-full w-full object-cover">
                    </div>
                @endforeach
            </div>
            <div class="mbs-showcase-panel">
                <p class="text-xs font-bold uppercase tracking-widest text-primary">Editor's Pick</p>
                <h3 class="mt-3 text-2xl font-bold text-navy lg:text-3xl">{{ $showcaseProduct['name'] }}</h3>
                <p class="mt-4 text-sm leading-relaxed text-muted">Premium quality with official warranty. Immersive sound and cinematic experience for your living room.</p>
                <div class="mt-4 flex items-center gap-2">
                    <span class="star-rating">{{ Mbs::stars($showcaseProduct['rating']) }}</span>
                    <span class="text-sm text-muted">({{ $showcaseProduct['review_count'] }} reviews)</span>
                </div>
                <p class="mt-4 text-3xl font-bold text-primary">{{ Mbs::price($showcaseProduct['price']) }}</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('shop') }}" class="mbs-btn mbs-btn-primary">View Product</a>
                    <button type="button" @click="cartOpen = true" class="mbs-btn mbs-btn-outline">Add to Cart</button>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- Featured collections --}}
<section class="home-section bg-secondary">
    <div class="mbs-container">
        @include('components.section-header', ['title' => 'Featured Collections', 'subtitle' => 'Curated collections for every lifestyle'])
        <div class="home-section-inner grid gap-5 md:grid-cols-2">
            @foreach ($featuredCollections as $collection)
                <a href="{{ route($collection['href']) }}" class="mbs-collection-card group">
                    <img src="{{ Mbs::image($collection['image']) }}" alt="{{ $collection['title'] }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-navy/85 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                        <h3 class="text-2xl font-bold">{{ $collection['title'] }}</h3>
                        <p class="mt-1 text-sm text-blue-100">{{ $collection['subtitle'] }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Shop by brand --}}
<section class="home-section bg-gradient-to-b from-primary-light/50 to-white">
    <div class="mbs-container">
        @include('components.section-header', ['title' => 'Shop By Brand', 'subtitle' => 'Explore products from trusted brands', 'viewAllHref' => route('shop')])
        <div class="home-section-inner grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7">
            @foreach ($brands as $brand)
                <a href="{{ route('shop') }}" class="mbs-brand-card">
                    <span class="text-sm font-bold text-navy">{{ $brand['name'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- AI Smart Shopping --}}
<section class="home-section mbs-ai-section">
    <div class="mbs-container">
        @include('components.section-header', ['title' => 'AI Smart Shopping', 'subtitle' => 'Personalized recommendations powered by smart search'])
        <div class="home-section-inner grid gap-5 md:grid-cols-3">
            @foreach ($aiFeatures as $feature)
                <div class="mbs-ai-card">
                    <h3 class="text-lg font-bold">{{ $feature['title'] }}</h3>
                    <p class="mt-2 text-sm text-blue-100">{{ $feature['description'] }}</p>
                </div>
            @endforeach
        </div>
        <div class="mt-8 text-center">
            <button type="button" @click="searchOpen = true" class="mbs-btn mbs-btn-white">Try Smart Search</button>
        </div>
    </div>
</section>

{{-- Customer reviews --}}
<section class="home-section bg-white">
    <div class="mbs-container">
        @include('components.section-header', ['title' => 'What Customers Say', 'subtitle' => 'Real feedback from MyBestStore shoppers'])
        <div class="home-section-inner grid gap-5 md:grid-cols-3">
            @foreach ($reviews as $review)
                <div class="mbs-review-card">
                    <div class="star-rating text-lg">{{ str_repeat('★', $review['rating']) }}</div>
                    <p class="mt-4 text-sm leading-relaxed text-muted">"{{ $review['text'] }}"</p>
                    <p class="mt-4 text-sm font-bold text-navy">{{ $review['name'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FAQ --}}
<section id="faq" class="home-section bg-secondary">
    <div class="mx-auto max-w-3xl px-4 lg:px-6">
        @include('components.section-header', ['title' => 'Frequently Asked Questions', 'centered' => true])
        <div class="home-section-inner space-y-3">
            @foreach ($faqs as $faq)
                <details class="mbs-faq-item group">
                    <summary class="cursor-pointer list-none font-bold text-navy marker:hidden">{{ $faq['q'] }}</summary>
                    <p class="mt-3 text-sm leading-relaxed text-muted">{{ $faq['a'] }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>

{{-- Blog --}}
<section class="home-section bg-white">
    <div class="mbs-container">
        @include('components.section-header', ['title' => 'Guides, Tips & News', 'subtitle' => 'Expert advice for smarter shopping', 'viewAllHref' => route('blog')])
        <div class="home-section-inner grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach (array_slice($posts, 0, 3) as $post)
                @include('components.blog-card', ['post' => $post])
            @endforeach
        </div>
    </div>
</section>
@endsection
