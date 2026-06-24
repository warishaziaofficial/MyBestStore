{{-- Homepage hero — original layout; main carousel content from CMS when available --}}
@php use App\Support\Mbs; @endphp

@php
    $defaultHeroSlides = [
        [
            'image' => asset('assets/images/hero/hero-digital-hardware-product.jpg'),
            'alt' => 'Zebra ZT410 barcode label printer',
            'label' => 'Digitalwares Collection',
            'title' => 'Best Digital Hardware Store in Pakistan',
            'subtitle' => 'Point of sale, time attendance, security, networking and computer systems.',
            'button' => 'Shop Now',
            'href' => route('shop'),
        ],
        [
            'image' => asset('assets/images/hero/digitalwares-point-of-sales.jpg'),
            'alt' => 'Point of sales systems and POS hardware',
            'label' => 'Point of Sales',
            'title' => 'Complete POS Solutions',
            'subtitle' => 'Barcode scanners, label printers, thermal printers, cash drawers and POS systems.',
            'button' => 'Explore POS',
            'href' => Mbs::shopCategoryUrl('point-of-sales'),
        ],
        [
            'image' => asset('assets/images/hero/digitalwares-time-attendance.jpg'),
            'alt' => 'Time attendance and access control systems',
            'label' => 'Time Attendance',
            'title' => 'Time Attendance & Access Control',
            'subtitle' => 'Biometric devices, fingerprint readers, smart door locks and access terminals.',
            'button' => 'View Collection',
            'href' => Mbs::shopCategoryUrl('time-attendance-access-control'),
        ],
    ];

    $cmsHeroSlides = collect($heroSlides ?? [])->map(function (array $slide): array {
        return [
            'image' => Mbs::image($slide['image'] ?? ''),
            'alt' => $slide['title'] ?? 'Featured promotion',
            'label' => $slide['eyebrow'] ?? 'Featured',
            'title' => $slide['title'] ?? '',
            'subtitle' => $slide['subtitle'] ?? '',
            'button' => $slide['cta'] ?? 'Shop Now',
            'href' => Mbs::storefrontHref($slide['cta_href'] ?? 'shop'),
        ];
    })->filter(fn (array $slide) => filled($slide['title']))->values()->all();

    $heroMainSlides = $cmsHeroSlides !== [] ? $cmsHeroSlides : $defaultHeroSlides;
@endphp

<section class="hero-showcase" aria-label="Featured promotions">
    <div class="hero-showcase-grid">
        <div class="hero-main-carousel" data-hero-carousel>
            <div class="hero-main-carousel-track" aria-live="polite">
                @foreach ($heroMainSlides as $index => $slide)
                    <div class="hero-slide{{ $index === 0 ? ' active' : '' }}" data-hero-slide>
                        <img
                            src="{{ $slide['image'] }}"
                            alt="{{ $slide['alt'] }}"
                            class="hero-slide-bg{{ $index === 0 ? ' hero-slide-bg--banner' : '' }}"
                            width="1920"
                            height="1080"
                            @if ($index > 0) loading="lazy" @endif
                        >
                        <div class="hero-overlay hero-overlay--main" aria-hidden="true"></div>
                        <div class="hero-content hero-content--main">
                            <span class="hero-label">{{ $slide['label'] }}</span>
                            <h1 class="hero-title">{{ $slide['title'] }}</h1>
                            <p class="hero-subtitle">{{ $slide['subtitle'] }}</p>
                            <a href="{{ $slide['href'] }}" class="hero-btn">{{ $slide['button'] }}</a>
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
            <a href="{{ Mbs::shopCategoryUrl('time-attendance-access-control') }}" class="hero-side-card">
                <img
                    src="{{ asset('assets/images/hero/digitalwares-access-control.jpg') }}"
                    alt="Biometric access control device"
                    class="hero-side-card-bg"
                    width="1200"
                    height="900"
                >
                <div class="hero-overlay hero-overlay--side" aria-hidden="true"></div>
                <div class="hero-content hero-content--side">
                    <span class="hero-label hero-label--accent">Time Attendance</span>
                    <h2 class="hero-title">Access Control</h2>
                    <span class="hero-btn hero-btn--link">Shop Now</span>
                </div>
            </a>

            <a href="{{ Mbs::shopCategoryUrl('point-of-sales') }}" class="hero-side-card">
                <img
                    src="{{ asset('assets/images/hero/digitalwares-point-of-sales.jpg') }}"
                    alt="POS terminal and receipt printer"
                    class="hero-side-card-bg"
                    width="1200"
                    height="900"
                >
                <div class="hero-overlay hero-overlay--side" aria-hidden="true"></div>
                <div class="hero-content hero-content--side">
                    <span class="hero-label hero-label--accent">Point of Sales</span>
                    <h2 class="hero-title">POS Hardware</h2>
                    <span class="hero-btn hero-btn--link">Shop Now</span>
                </div>
            </a>
        </div>
    </div>
</section>
