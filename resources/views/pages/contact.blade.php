@extends('layouts.app')
@php use App\Support\Mbs; @endphp

@section('title', 'DigitalWares | Contact')

@section('content')
@include('components.page-hero', [
    'title' => 'Contact Us',
    'description' => 'We’re here to help with your orders, product questions, support, and feedback.',
])

<section class="mbs-page-section">
    <div class="mbs-container">
        <div class="mbs-contact-grid">
            <form class="mbs-contact-form" x-data="{ ok: false }" @submit.prevent="ok = true">
                <h2 class="mbs-section-title">Send Message</h2>
                <p class="mbs-section-subtitle">Fill out the form and our team will get back to you shortly.</p>
                <div class="mbs-form-grid mbs-form-grid--2">
                    <input required class="mbs-input" placeholder="Name *">
                    <input required type="email" class="mbs-input" placeholder="Email *">
                </div>
                <div class="mbs-form-grid mbs-form-grid--2">
                    <input class="mbs-input" placeholder="Phone">
                    <input class="mbs-input" placeholder="Subject">
                </div>
                <textarea required class="mbs-input" placeholder="Message *"></textarea>
                <label class="mt-4 flex items-center gap-2 text-sm text-muted">
                    <input type="checkbox" class="rounded border-border text-primary focus:ring-primary">
                    Save my name, email, and phone for next time.
                </label>
                <button type="submit" class="mbs-btn mbs-btn-primary">Send Message</button>
                <p x-show="ok" x-cloak class="mt-3 text-sm font-semibold text-primary">Thank you! Your message has been received.</p>
            </form>
            <div class="mbs-contact-image">
                <img src="{{ Mbs::image('banners/audio-entertainment.jpg') }}" alt="Customer support">
            </div>
        </div>
    </div>
</section>

<section class="mbs-trust-section mbs-page-section">
    <div class="mbs-container">
        @include('components.section-header', ['title' => 'DigitalWares Support', 'subtitle' => 'Reach the right team for your request'])
        <div class="home-section-inner grid gap-5 md:grid-cols-3">
            @foreach ($contactCards as $card)
                <div class="mbs-support-card">
                    <h3 class="font-bold text-navy">{{ $card['title'] }}</h3>
                    <p class="mt-3 text-lg font-bold text-primary">{{ $card['value'] }}</p>
                    <p class="mt-2 text-sm text-muted">{{ $card['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="mbs-page-section">
    <div class="mbs-container">
        @include('components.section-header', [
            'title' => $contactMap['title'] ?? 'Find Us on the Map',
            'subtitle' => $contactMap['subtitle'] ?? 'Plan your visit or get directions to our store',
        ])
        <div class="mbs-map-card">
            @if (!empty($contactMap['embed_url']))
                <iframe
                    class="mbs-map-embed"
                    src="{{ $contactMap['embed_url'] }}"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="DigitalWares location on Google Maps"
                ></iframe>
            @else
                <div class="mbs-map-placeholder">Map placeholder — connect Google Maps via CMS later</div>
            @endif
        </div>
        @if (!empty($contactMap['address']) || !empty($contactMap['maps_link']))
            <div class="mbs-map-footer">
                @if (!empty($contactMap['address']))
                    <p class="mbs-map-address">{{ $contactMap['address'] }}</p>
                @endif
                @if (!empty($contactMap['maps_link']))
                    <a href="{{ $contactMap['maps_link'] }}" target="_blank" rel="noopener noreferrer" class="mbs-map-link">
                        Open in Google Maps →
                    </a>
                @endif
            </div>
        @endif
        <div class="mt-6 flex flex-wrap gap-4">
            <a href="{{ route('shop') }}" class="mbs-section-link">Browse Shop →</a>
            <a href="{{ route('blog') }}" class="mbs-section-link">Read Guides →</a>
            <a href="{{ route('new-arrivals') }}" class="mbs-section-link">New Arrivals →</a>
        </div>
    </div>
</section>
@endsection
