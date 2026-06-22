@extends('layouts.app')
@php use App\Support\Mbs; @endphp

@section('title', 'MyBestStore | About Us')

@section('content')
<div class="about-page">
    {{-- 1. Hero --}}
    <section class="about-hero">
        <div class="mbs-container about-hero-inner">
            <nav class="about-hero-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span aria-hidden="true">&gt;</span>
                <span>About Us</span>
            </nav>
            <h1 class="about-hero-title">About Us</h1>
        </div>
    </section>

    {{-- 2. Intro --}}
    <section class="about-intro">
        <div class="mbs-container about-intro-grid">
            <div class="about-intro-content">
                <p class="about-intro-label">About Us</p>
                <h2 class="about-intro-heading">Pakistan’s Trusted Premium Electronics Store</h2>
                <p><strong>MyBestStore.pk</strong> is a premium ecommerce destination for electronics, audio products, smart home appliances and accessories across Pakistan.</p>
                <p>We curate genuine products from leading brands — Samsung, Sony, TCL, Panasonic and more — so you can shop with confidence. Every listing is selected for quality, warranty coverage and real-world performance in Pakistani homes.</p>
                <p>From fast nationwide delivery to flexible payments and responsive support, MyBestStore is built to make premium electronics accessible, transparent and reliable for every customer.</p>
                <p class="about-intro-signature">MyBestStore Team</p>
            </div>
            <div class="about-intro-image">
                <img
                    src="{{ Mbs::image('assets/images/about/about-main.jpg') }}"
                    alt="Premium home audio and electronics at MyBestStore"
                    loading="lazy"
                >
            </div>
        </div>
    </section>

    {{-- 3. How We Are Unique --}}
    <section class="about-unique" style="--about-unique-bg: url('{{ Mbs::image('assets/images/about/about-unique-bg.jpg') }}');">
        <div class="mbs-container about-unique-grid">
            <div class="about-unique-main">
                <header class="about-unique-header">
                    <h2 class="about-unique-title">How We Are Unique</h2>
                    <p class="about-unique-subtitle">We combine premium products, trusted service and reliable support.</p>
                </header>

                <div class="about-feature-grid">
                    @php
                        $features = [
                            ['title' => 'Genuine Products', 'icon' => 'shield'],
                            ['title' => 'Quality Checked', 'icon' => 'check'],
                            ['title' => 'Original Warranty', 'icon' => 'warranty'],
                            ['title' => 'Best Price', 'icon' => 'tag'],
                            ['title' => 'Fast Delivery', 'icon' => 'truck'],
                            ['title' => '24/7 Support', 'icon' => 'support'],
                        ];
                    @endphp
                    @foreach ($features as $feature)
                        <article class="about-feature-card">
                            <span class="about-feature-icon" aria-hidden="true">
                                @if ($feature['icon'] === 'shield')
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 3 4 7v6c0 5 3.5 9.5 8 10 4.5-.5 8-5 8-10V7l-8-4Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="m9 12 2 2 4-4"/></svg>
                                @elseif ($feature['icon'] === 'check')
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                @elseif ($feature['icon'] === 'warranty')
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z"/></svg>
                                @elseif ($feature['icon'] === 'tag')
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 14 6 11l7-7 3 3-7 7Zm0 0 2 2m-2-2-2 2m8-5 2 2"/></svg>
                                @elseif ($feature['icon'] === 'truck')
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8.25 18.75a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3ZM15.75 18.75a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 6h11v9H3V6Zm11 0 3.5 3.5V15H14"/></svg>
                                @else
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8.625 9.75a3.375 3.375 0 1 1 6.75 0 3.375 3.375 0 0 1-6.75 0ZM12 14.25c-3.314 0-6 1.79-6 4v.75h12v-.75c0-2.21-2.686-4-6-4Z"/></svg>
                                @endif
                            </span>
                            <h3 class="about-feature-title">{{ $feature['title'] }}</h3>
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="about-overlap-images" aria-hidden="true">
                <img class="about-overlap-images-primary" src="{{ Mbs::image('assets/images/about/about-unique-1.jpg') }}" alt="">
                <img class="about-overlap-images-secondary" src="{{ Mbs::image('assets/images/about/about-unique-2.jpg') }}" alt="">
            </div>
        </div>
    </section>

    {{-- 4. Testimonials --}}
    <section class="about-testimonials">
        <div class="mbs-container">
            <h2 class="about-testimonials-title">Our Customers’ Comments</h2>

            <div
                class="about-testimonials-slider"
                x-data="{
                    index: 0,
                    total: {{ count($testimonials) }},
                    prev() { if (this.index > 0) this.index--; },
                    next() { if (this.index < this.total - 1) this.index++; }
                }"
            >
                <button type="button" class="about-testimonials-arrow about-testimonials-arrow--prev" @click="prev()" :disabled="index === 0" aria-label="Previous comment">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>

                <div class="about-testimonials-body">
                    @foreach ($testimonials as $i => $review)
                        @php
                            $nameParts = preg_split('/\s+/', trim($review['name']));
                            $initials = '';
                            foreach ($nameParts as $part) {
                                $initials .= strtoupper(substr($part, 0, 1));
                            }
                            $initials = substr($initials, 0, 2);
                        @endphp
                        <article class="about-testimonials-slide" x-show="index === {{ $i }}" x-cloak>
                            <div class="about-testimonials-quote" aria-hidden="true">
                                <svg fill="currentColor" viewBox="0 0 24 24"><path d="M7.17 6A5.001 5.001 0 0 0 2 11c0 2.76 2.24 5 5 5 .55 0 1-.45 1-1v-1.08c0-.55-.45-1-1-1-1.38 0-2.5-1.12-2.5-2.5 0-1.2.86-2.2 2-2.45V6H7.17zm10 0A5.001 5.001 0 0 0 12 11c0 2.76 2.24 5 5 5 .55 0 1-.45 1-1v-1.08c0-.55-.45-1-1-1-1.38 0-2.5-1.12-2.5-2.5 0-1.2.86-2.2 2-2.45V6h-1.83z"/></svg>
                            </div>
                            <blockquote class="about-testimonials-text">&ldquo;{{ $review['text'] }}&rdquo;</blockquote>
                            <div class="about-testimonials-author">
                                @if (!empty($review['avatar']))
                                    <img src="{{ Mbs::image($review['avatar']) }}" alt="{{ $review['name'] }}" class="about-testimonials-avatar">
                                @else
                                    <span class="about-testimonials-avatar about-testimonials-avatar--initials">{{ $initials }}</span>
                                @endif
                                <div>
                                    <p class="about-testimonials-name">{{ $review['name'] }}</p>
                                    <p class="about-testimonials-label">Verified Buyer</p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <button type="button" class="about-testimonials-arrow about-testimonials-arrow--next" @click="next()" :disabled="index >= total - 1" aria-label="Next comment">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </section>

    {{-- 5. Latest Guides --}}
    <section class="about-news">
        <div class="mbs-container">
            <header class="about-news-header">
                <div>
                    <h2 class="about-news-title">Latest Guides</h2>
                    <p class="about-news-subtitle">Expert buying advice to help you choose the right electronics for your home.</p>
                </div>
            </header>

            <div class="about-news-grid">
                @foreach ($guidePosts as $post)
                    <article class="about-news-card">
                        <a href="{{ route('blog') }}#post-{{ $post['slug'] }}" class="about-news-card-media">
                            <img src="{{ Mbs::image($post['image']) }}" alt="{{ $post['title'] }}" loading="lazy">
                        </a>
                        <div class="about-news-card-body">
                            <time class="about-news-date" datetime="{{ $post['date'] }}">{{ $post['date'] }}</time>
                            <h3 class="about-news-card-title">
                                <a href="{{ route('blog') }}#post-{{ $post['slug'] }}">{{ $post['title'] }}</a>
                            </h3>
                            <p class="about-news-excerpt">{{ $post['excerpt'] }}</p>
                            <a href="{{ route('blog') }}#post-{{ $post['slug'] }}" class="about-news-link">Read More <span aria-hidden="true">→</span></a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="about-news-action">
                <a href="{{ route('product-guides') }}" class="about-news-btn">View All</a>
            </div>
        </div>
    </section>

    {{-- 6. Trust Strip --}}
    <section class="about-trust-strip" aria-label="Why trust MyBestStore">
        <div class="mbs-container about-trust-strip-inner">
            @php
                $trustItems = [
                    ['title' => 'Original Products', 'desc' => '100% genuine with official warranty', 'icon' => 'box'],
                    ['title' => '30 Days Warranty', 'desc' => 'Manufacturer-backed coverage support', 'icon' => 'shield'],
                    ['title' => 'Easy Returns', 'desc' => 'Hassle-free return process', 'icon' => 'return'],
                    ['title' => 'Free Shipping', 'desc' => 'Free delivery on selected orders', 'icon' => 'ship'],
                ];
            @endphp
            @foreach ($trustItems as $item)
                <div class="about-trust-item">
                    <span class="about-trust-icon" aria-hidden="true">
                        @if ($item['icon'] === 'box')
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="m20 7-8-4-8 4m16 0-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        @elseif ($item['icon'] === 'shield')
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 3 4 7v6c0 5 3.5 9.5 8 10 4.5-.5 8-5 8-10V7l-8-4Z"/></svg>
                        @elseif ($item['icon'] === 'return')
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/></svg>
                        @else
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8.25 18.75a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3ZM15.75 18.75a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 6h11v9H3V6Zm11 0 3.5 3.5V15H14"/></svg>
                        @endif
                    </span>
                    <div>
                        <h3 class="about-trust-title">{{ $item['title'] }}</h3>
                        <p class="about-trust-desc">{{ $item['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
