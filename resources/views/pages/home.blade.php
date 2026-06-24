@extends('layouts.app')
@php use App\Support\Mbs; @endphp

@section('title', 'DigitalWares | Premium Electronics')

@section('content')
@include('components.hero-grid')

{{-- Shop by brand --}}
<section class="home-section bg-gradient-to-b from-primary-light/50 to-white">
    <div class="mbs-container">
        @include('components.section-header', ['title' => 'Shop By Brand', 'subtitle' => 'Explore products from trusted brands', 'viewAllHref' => route('categories')])
        <div class="home-section-inner grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-7 max-w-6xl mx-auto">
            @foreach ($brands as $brand)
                <a href="{{ route('shop', ['brand' => $brand['name']]) }}" class="mbs-brand-card" title="{{ $brand['name'] }}">
                    <img
                        src="{{ Mbs::image($brand['logo'] ?? '') }}"
                        alt="{{ $brand['name'] }}"
                        class="mbs-brand-card-logo mbs-brand-card-logo--lg"
                        loading="lazy"
                    >
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Special Offers — premium deals carousel --}}
<section
    id="deals"
    class="home-section bg-white deals-showcase special-offers"
    x-data="{
        activeSlide: 0,
        totalSlides: {{ count($specialOffersSlides) }},
        timer: null,
        isPaused: false,
        init() { this.play(); },
        play() {
            clearInterval(this.timer);
            if (this.isPaused) return;
            this.timer = setInterval(() => this.next(true), 4500);
        },
        pause() {
            this.isPaused = true;
            clearInterval(this.timer);
        },
        resume() {
            this.isPaused = false;
            this.play();
        },
        next(fromTimer = false) {
            this.activeSlide = (this.activeSlide + 1) % this.totalSlides;
            if (!fromTimer) this.play();
        },
        prev() {
            this.activeSlide = (this.activeSlide - 1 + this.totalSlides) % this.totalSlides;
            this.play();
        }
    }"
    x-init="init()"
    @mouseenter="pause()"
    @mouseleave="resume()"
>
    <div class="mbs-container">
        <div class="deals-showcase-header special-offers-header">
            <div class="deals-icon-badge special-offers-icon" aria-hidden="true">
                <svg class="deals-icon-badge-svg special-offers-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 7h.01"/>
                </svg>
            </div>
            <div class="deals-showcase-heading">
                <h2 class="deals-showcase-title">Special Offers</h2>
                <p class="deals-showcase-subtitle">Barcode printers, scanners, CCTV cameras, laptops and business equipment from our latest catalog.</p>
            </div>
            <div class="deals-showcase-nav">
                <button type="button" class="deals-nav-btn" aria-label="Previous deals" @click="prev()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" class="deals-nav-btn" aria-label="Next deals" @click="next()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        <div class="deals-carousel" aria-live="polite">
            <div class="deals-carousel-track" :style="`transform: translateX(-${activeSlide * 100}%)`">
                @foreach ($specialOffersSlides as $slide)
                    <div class="deals-slide">
                        <div class="deals-banner-grid">
                            @foreach (['left' => '', 'right' => ' deals-banner-large offer-banner--large'] as $side => $bannerClass)
                                @if (! empty($slide[$side]))
                                    @php $offer = $slide[$side]; @endphp
                                    <a
                                        href="{{ ! empty($offer['slug']) ? route('product.show', $offer['slug']) : route('shop') }}"
                                        class="deals-banner offer-banner{{ $bannerClass }}"
                                    >
                                        <img
                                            src="{{ Mbs::image($offer['image']) }}"
                                            alt="{{ $offer['alt'] }}"
                                            class="deals-banner-bg offer-banner-image"
                                            loading="lazy"
                                        >
                                        <div class="deals-banner-overlay offer-banner-overlay"></div>
                                        <div class="deals-banner-content offer-banner-content">
                                            <h3 class="deals-banner-title">{{ $offer['title'] }}</h3>
                                            <div class="deals-banner-prices">
                                                @if (($offer['price'] ?? 0) > 0)
                                                    <span class="deals-banner-price">{{ Mbs::price($offer['price']) }}</span>
                                                    @if (($offer['old_price'] ?? 0) > ($offer['price'] ?? 0))
                                                        <span class="deals-banner-price-old">{{ Mbs::price($offer['old_price']) }}</span>
                                                    @endif
                                                @else
                                                    <span class="deals-banner-price">Contact for Price</span>
                                                @endif
                                            </div>
                                            <span class="deals-banner-btn">Shop Now</span>
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Featured Collections --}}
<section class="home-section featured-collections-strip">
    <div class="mbs-container">
        <div class="featured-collections-strip-inner">
            <aside class="featured-collections-promo-panel">
                <h2 class="featured-collections-promo-title">Curated Collections</h2>
                <p class="featured-collections-promo-desc">More picks from our catalog — printers, biometrics and POS gear not shown above.</p>
                <a href="{{ route('shop') }}" class="featured-collections-promo-btn">Explore All</a>
            </aside>
            <div class="featured-collections-items">
                @foreach (array_slice($featuredCollections, 0, 4) as $collection)
                    @php
                        $collectionUrl = ! empty($collection['slug'])
                            ? route('product.show', $collection['slug'])
                            : (! empty($collection['category'])
                                ? Mbs::shopCategoryUrl($collection['category'])
                                : route($collection['href'] ?? 'shop'));
                    @endphp
                    <a href="{{ $collectionUrl }}" class="featured-collection-tile">
                        <div class="featured-collection-image-wrap">
                            <img
                                src="{{ Mbs::image($collection['image']) }}"
                                alt="{{ $collection['title'] }}"
                                class="featured-collection-image"
                                loading="lazy"
                            >
                        </div>
                        <div class="featured-collection-info">
                            <h3 class="featured-collection-name">{{ $collection['title'] }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Shop By Category --}}
<section class="home-section mbs-shop-category-section">
    <div class="mbs-container">
        @include('components.section-header', ['title' => 'Premium Product Categories', 'subtitle' => 'Barcode printers, scanners, CCTV cameras and access control from our latest catalog', 'viewAllHref' => route('categories')])
        <div class="home-section-inner mbs-shop-category-grid">
            @foreach (array_slice($premiumCategories, 0, 4) as $category)
                @include('components.shop-category-tile', ['category' => $category])
            @endforeach
        </div>
    </div>
</section>

@include('components.product-grid-section', ['title' => 'Best Selling Products', 'subtitle' => 'Top picks loved by customers', 'products' => array_slice($bestSelling, 0, 4), 'viewAllHref' => route('shop'), 'bg' => 'bg-white'])
@include('components.product-grid-section', ['title' => 'New Arrivals', 'subtitle' => 'Recently added to DigitalWares', 'products' => array_slice($newArrivals, 0, 4), 'viewAllHref' => route('new-arrivals'), 'bg' => 'bg-secondary'])

{{-- Trust strip --}}
<section class="trust-strip" aria-label="Why shop with DigitalWares">
    <div class="mbs-container">
        <div class="trust-strip-grid">
            @php
                $trustItems = [
                    ['title' => 'Free Shipping', 'desc' => 'Free delivery on selected orders', 'icon' => 'ship'],
                    ['title' => '100% Secure Payment', 'desc' => 'Safe and encrypted checkout', 'icon' => 'shield'],
                    ['title' => '24/7 Customer Support', 'desc' => 'Expert help whenever you need it', 'icon' => 'support'],
                    ['title' => 'Free & Easy Returns', 'desc' => 'Hassle-free product returns', 'icon' => 'return'],
                ];
            @endphp
            @foreach ($trustItems as $item)
                <article class="trust-strip-card">
                    <span class="trust-strip-card-icon" aria-hidden="true">
                        @if ($item['icon'] === 'ship')
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 18H9"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="7" cy="18" r="2" stroke-width="1.75"/><circle cx="17" cy="18" r="2" stroke-width="1.75"/></svg>
                        @elseif ($item['icon'] === 'shield')
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="m9 12 2 2 4-4"/></svg>
                        @elseif ($item['icon'] === 'support')
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 11h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H3v-5Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 11h-3a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h3v-5Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 11V8a9 9 0 0 1 18 0v3"/></svg>
                        @else
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 12v4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="m9 15 3 3 3-3"/></svg>
                        @endif
                    </span>
                    <h3 class="trust-strip-card-title">{{ $item['title'] }}</h3>
                    <p class="trust-strip-card-desc">{{ $item['desc'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<div class="mbs-section-separator" aria-hidden="true"></div>

{{-- AI Smart Shopping --}}
<section class="home-section mbs-ai-section">
    <div class="mbs-container">
        @include('components.section-header', ['title' => 'AI Smart Shopping', 'subtitle' => 'Personalized recommendations powered by smart search'])
        <div class="home-section-inner grid gap-5 md:grid-cols-3 items-stretch">
            @foreach ($aiFeatures as $feature)
                <div class="mbs-ai-card">
                    <div class="mbs-ai-card-icon" aria-hidden="true">
                        @if ($loop->index === 0)
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"/></svg>
                        @elseif ($loop->index === 1)
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 4.5v15m6-15v15M4.5 9h15M4.5 15h15"/></svg>
                        @else
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 13.5h6m-8 0V18a2.25 2.25 0 0 0 2.25 2.25H9A2.25 2.25 0 0 1 6.75 18v-2.25m12 0V18a2.25 2.25 0 0 1-2.25 2.25h-1.5A2.25 2.25 0 0 1 13.5 18v-2.25m0-9V4.875c0-.621-.504-1.125-1.125-1.125h-3.75c-.621 0-1.125.504-1.125 1.125v3.375"/></svg>
                        @endif
                    </div>
                    <h3 class="mbs-ai-card-title">{{ $feature['title'] }}</h3>
                    <p class="mbs-ai-card-desc">{{ $feature['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Customer reviews --}}
<section class="home-section testimonials-section">
    <div class="mbs-container">
        <header class="testimonials-header">
            <h2 class="testimonials-header-title">What Customers Say</h2>
            <p class="testimonials-header-subtitle">Real feedback from happy DigitalWares shoppers across Pakistan.</p>
        </header>

        <div
            class="testimonials-slider"
            x-data="{
                index: 0,
                perView: 1,
                total: {{ count($reviews) }},
                init() { this.updatePerView(); },
                updatePerView() {
                    if (window.innerWidth >= 1024) this.perView = 3;
                    else if (window.innerWidth >= 640) this.perView = 2;
                    else this.perView = 1;
                    if (this.index > this.maxIndex) this.index = this.maxIndex;
                },
                get maxIndex() { return Math.max(0, this.total - this.perView); },
                scrollTo(index) {
                    this.index = index;
                    const track = this.$refs.track;
                    const card = track.querySelector('.testimonial-card');
                    if (!card) return;
                    const gap = 20;
                    track.scrollTo({ left: index * (card.offsetWidth + gap), behavior: 'smooth' });
                },
                prev() { if (this.index > 0) this.scrollTo(this.index - 1); },
                next() { if (this.index < this.maxIndex) this.scrollTo(this.index + 1); }
            }"
            x-init="init()"
            @resize.window="updatePerView()"
        >
            <div class="testimonials-slider-controls">
                <button type="button" class="testimonials-arrow testimonials-arrow--prev" @click="prev()" :disabled="index === 0" aria-label="Previous reviews">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" class="testimonials-arrow testimonials-arrow--next" @click="next()" :disabled="index >= maxIndex" aria-label="Next reviews">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <div class="testimonials-viewport" x-ref="track">
                <div class="testimonials-grid">
                    @foreach ($reviews as $review)
                        @php
                            $nameParts = preg_split('/\s+/', trim($review['name']));
                            $initials = '';
                            foreach ($nameParts as $part) {
                                $initials .= strtoupper(substr($part, 0, 1));
                            }
                            $initials = substr($initials, 0, 2);
                        @endphp
                        <article class="testimonial-card">
                            <div class="testimonial-quote-icon" aria-hidden="true">
                                <svg fill="currentColor" viewBox="0 0 24 24"><path d="M7.17 6A5.001 5.001 0 0 0 2 11c0 2.76 2.24 5 5 5 .55 0 1-.45 1-1v-1.08c0-.55-.45-1-1-1-1.38 0-2.5-1.12-2.5-2.5 0-1.2.86-2.2 2-2.45V6H7.17zm10 0A5.001 5.001 0 0 0 12 11c0 2.76 2.24 5 5 5 .55 0 1-.45 1-1v-1.08c0-.55-.45-1-1-1-1.38 0-2.5-1.12-2.5-2.5 0-1.2.86-2.2 2-2.45V6h-1.83z"/></svg>
                            </div>
                            <div class="testimonial-stars">
                                @include('components.product-stars', ['rating' => $review['rating']])
                            </div>
                            <p class="testimonial-text">&ldquo;{{ $review['text'] }}&rdquo;</p>
                            <div class="testimonial-author">
                                @if (!empty($review['avatar']))
                                    <img
                                        src="{{ Mbs::image($review['avatar']) }}"
                                        alt="{{ $review['name'] }}"
                                        class="testimonial-avatar testimonial-avatar--photo"
                                        loading="lazy"
                                    >
                                @else
                                    <span class="testimonial-avatar" aria-hidden="true">{{ $initials }}</span>
                                @endif
                                <div class="testimonial-author-meta">
                                    <p class="testimonial-author-name">{{ $review['name'] }}</p>
                                    <span class="testimonial-badge">Verified Buyer</span>
                                    @if (!empty($review['purchase']))
                                        <span class="testimonial-purchase">{{ $review['purchase'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="testimonials-dots" role="tablist" aria-label="Review slides">
                <template x-for="dot in (maxIndex + 1)" :key="dot">
                    <button
                        type="button"
                        class="testimonials-dot"
                        :class="{ 'is-active': index === (dot - 1) }"
                        @click="scrollTo(dot - 1)"
                        :aria-label="`Go to review slide ${dot}`"
                    ></button>
                </template>
            </div>
        </div>
    </div>
</section>

{{-- FAQ --}}
<section id="faq" class="home-section faq-section">
    <div class="mbs-container">
        <header class="faq-header">
            <h2 class="faq-header-title">Got A Query? We Are Glad To Assist</h2>
            <p class="faq-header-subtitle">Find quick answers about orders, delivery, payments, returns and product support.</p>
        </header>
        <div class="faq-grid" x-data="{ open: 0 }">
            @foreach ($faqs as $index => $faq)
                <div class="faq-item" :class="{ 'is-open': open === {{ $index }} }">
                    <button
                        type="button"
                        class="faq-question"
                        @click="open = open === {{ $index }} ? null : {{ $index }}"
                        :aria-expanded="open === {{ $index }}"
                    >
                        <span class="faq-question-text">{{ $faq['q'] }}</span>
                        <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer-wrap" :aria-hidden="open !== {{ $index }}">
                        <div class="faq-answer">
                            <p>{{ $faq['a'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Blog --}}
<section class="home-section bg-white">
    <div class="mbs-container">
        @include('components.section-header', ['title' => 'Guides, Tips & News', 'subtitle' => 'Buying guides for POS, barcode, security and retail hardware', 'viewAllHref' => route('blog')])
        <div class="home-section-inner grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach (array_slice($posts, 0, 3) as $post)
                @include('components.blog-card', ['post' => $post])
            @endforeach
        </div>
    </div>
</section>

<button
    type="button"
    class="mbs-scroll-top"
    x-data="{ visible: false }"
    x-init="
        const update = () => { visible = window.scrollY > 400 };
        window.addEventListener('scroll', update, { passive: true });
        update();
    "
    x-show="visible"
    x-transition
    x-cloak
    @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
    aria-label="Back to top"
>
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5M5 12l7-7 7 7"/>
    </svg>
</button>
@endsection
