@php
    use App\Support\Mbs;
    $slides = $slides ?? [];
@endphp

{{-- Rendered from: resources/views/pages/home.blade.php → @include('components.hero-slider') --}}
<section class="mbs-hero" x-data="{ active: 0, total: {{ count($slides) }} }" x-init="setInterval(() => { active = (active + 1) % total }, 6000)">
    <div class="mbs-hero-track">
        @foreach ($slides as $index => $slide)
            <div
                class="mbs-hero-slide{{ $index === 0 ? ' is-active' : '' }}"
                x-bind:class="{ 'is-active': active === {{ $index }} }"
            >
                <img
                    src="{{ Mbs::image($slide['image']) }}"
                    alt=""
                    class="mbs-hero-bg"
                >
                <div class="mbs-hero-overlay"></div>
                <div class="mbs-hero-inner">
                    <div class="mbs-hero-copy">
                        <p class="mbs-hero-eyebrow">{{ $slide['eyebrow'] }}</p>
                        <h1 class="mbs-hero-title">{{ $slide['title'] }}</h1>
                        <p class="mbs-hero-subtitle">{{ $slide['subtitle'] }}</p>
                        <div class="mbs-hero-actions">
                            <a href="{{ route($slide['cta_href']) }}" class="mbs-hero-btn mbs-hero-btn--primary">{{ $slide['cta'] }}</a>
                            <a href="{{ route($slide['secondary_href']) }}" class="mbs-hero-btn mbs-hero-btn--secondary">{{ $slide['secondary'] }}</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mbs-hero-dots" role="tablist" aria-label="Hero slides">
        @foreach ($slides as $index => $slide)
            <button
                type="button"
                class="mbs-hero-dot"
                x-bind:class="{ 'is-active': active === {{ $index }} }"
                @click="active = {{ $index }}"
                aria-label="Go to slide {{ $index + 1 }}"
            ></button>
        @endforeach
    </div>
</section>
