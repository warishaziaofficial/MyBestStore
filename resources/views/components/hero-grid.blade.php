{{-- Homepage hero showcase — resources/views/pages/home.blade.php --}}
@php
    $heroMainSlides = [
        [
            'image' => 'assets/images/hero/hero-main-1.jpg',
            'alt' => 'Discover premium tech for every lifestyle',
            'label' => 'Premium Electronics',
            'title' => 'Discover Premium Tech For Every Lifestyle',
            'subtitle' => 'Shop audio, smart gadgets, TVs and accessories across Pakistan.',
            'button' => 'Shop Now',
            'href' => 'shop',
        ],
        [
            'image' => 'assets/images/hero/hero-main-2.jpg',
            'alt' => 'Upgrade your sound experience',
            'label' => 'Smart Audio',
            'title' => 'Upgrade Your Sound Experience',
            'subtitle' => 'Explore soundbars, speakers and wireless audio collections.',
            'button' => 'Explore Audio',
            'href' => 'shop',
        ],
        [
            'image' => 'assets/images/hero/hero-main-3.jpg',
            'alt' => 'Latest smart gadgets are here',
            'label' => 'New Arrivals',
            'title' => 'Latest Smart Gadgets Are Here',
            'subtitle' => 'Find trending electronics and accessories at MyBestStore.',
            'button' => 'View New Arrivals',
            'href' => 'new-arrivals',
        ],
    ];
@endphp

<section class="hero-showcase" aria-label="Featured promotions">
    <div class="hero-showcase-grid">
        <div class="hero-main-carousel" data-hero-carousel>
            <div class="hero-main-carousel-track" aria-live="polite">
                @foreach ($heroMainSlides as $index => $slide)
                    <div class="hero-slide{{ $index === 0 ? ' active' : '' }}" data-hero-slide>
                        <img
                            src="{{ asset($slide['image']) }}"
                            alt="{{ $slide['alt'] }}"
                            class="hero-slide-bg"
                            width="1920"
                            height="1080"
                            @if ($index > 0) loading="lazy" @endif
                        >
                        <div class="hero-content hero-content--main">
                            <span class="hero-label">{{ $slide['label'] }}</span>
                            <h1 class="hero-title">{{ $slide['title'] }}</h1>
                            <p class="hero-subtitle">{{ $slide['subtitle'] }}</p>
                            <a href="{{ route($slide['href']) }}" class="hero-btn">{{ $slide['button'] }}</a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="hero-carousel-controls">
                <button type="button" class="hero-prev" aria-label="Previous slide">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                @foreach ($heroMainSlides as $index => $slide)
                    <button
                        type="button"
                        class="hero-dot{{ $index === 0 ? ' active' : '' }}"
                        data-hero-dot="{{ $index }}"
                        aria-label="Go to slide {{ $index + 1 }}"
                    ></button>
                @endforeach
                <button type="button" class="hero-next" aria-label="Next slide">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        <div class="hero-side-column">
            <a href="{{ route('new-arrivals') }}" class="hero-side-card">
                <img
                    src="{{ asset('assets/images/hero/hero-side-1.jpg') }}"
                    alt="Wireless earbuds"
                    class="hero-side-card-bg"
                    width="1200"
                    height="900"
                >
                <div class="hero-content hero-content--side">
                    <span class="hero-label hero-label--accent">New Arrivals</span>
                    <h2 class="hero-title">Wireless Earbuds</h2>
                    <span class="hero-btn hero-btn--link">Shop Now</span>
                </div>
            </a>

            <a href="{{ route('shop') }}" class="hero-side-card">
                <img
                    src="{{ asset('assets/images/hero/hero-side-2.jpg') }}"
                    alt="HomePod Pro smart speaker"
                    class="hero-side-card-bg"
                    width="1200"
                    height="900"
                >
                <div class="hero-content hero-content--side">
                    <span class="hero-label hero-label--accent">Smart Audio</span>
                    <h2 class="hero-title">HomePod Pro</h2>
                    <span class="hero-btn hero-btn--link">Shop Now</span>
                </div>
            </a>
        </div>
    </div>
</section>
