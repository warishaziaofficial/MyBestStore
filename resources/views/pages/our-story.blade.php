@extends('layouts.app')
@php use App\Support\Mbs; @endphp

@section('title', 'MyBestStore | Our Story')

@section('content')
<div class="story-page">
    {{-- 1. Hero --}}
    <section class="story-hero">
        <div class="mbs-container story-hero-inner">
            <nav class="story-hero-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span aria-hidden="true">&gt;</span>
                <span>Our Story</span>
            </nav>
            <h1 class="story-hero-title">Our Story</h1>
        </div>
    </section>

    {{-- 2. Story Intro (image left, content right) --}}
    <section class="story-intro">
        <div class="mbs-container story-intro-grid">
            <div class="story-intro-image">
                <img
                    src="{{ Mbs::image('assets/images/story/story-intro.jpg') }}"
                    alt="MyBestStore journey in premium electronics"
                    loading="lazy"
                >
            </div>
            <div class="story-intro-content">
                <p class="story-intro-label">Our Journey</p>
                <h2 class="story-intro-heading">Building a Better Electronics Shopping Experience</h2>
                <p><strong>MyBestStore.pk</strong> was created with a simple goal: to make premium electronics, audio products, smart home appliances and accessories easier to discover, compare and buy across Pakistan.</p>
                <p>We built a platform where customers could trust what they see — genuine brands, clear pricing, and detailed product information that helps you choose confidently before checkout.</p>
                <p>From responsive customer support to nationwide delivery and official warranty guidance, every part of our journey has been shaped by listening to shoppers who wanted a better way to buy electronics online.</p>
                <p>Today, we continue expanding our collections while staying focused on quality products, honest recommendations, and a shopping experience that feels premium from the first click to delivery at your door.</p>
            </div>
        </div>
    </section>

    {{-- 3. Timeline --}}
    <section class="story-timeline">
        <div class="mbs-container">
            <header class="story-section-header">
                <p class="story-section-label">Milestones</p>
                <h2 class="story-section-title">The MyBestStore Journey</h2>
            </header>

            <ol class="story-timeline-list">
                @php
                    $milestones = [
                        [
                            'year' => '2023',
                            'title' => 'Idea Started',
                            'text' => 'We noticed customers needed a simpler way to shop quality electronics online.',
                            'icon' => 'lightbulb',
                        ],
                        [
                            'year' => '2024',
                            'title' => 'Product Collections Expanded',
                            'text' => 'We started adding premium audio, TVs, smart home and accessories.',
                            'icon' => 'grid',
                        ],
                        [
                            'year' => '2025',
                            'title' => 'Customer First Experience',
                            'text' => 'We focused on better product guidance, faster delivery and easy support.',
                            'icon' => 'heart',
                        ],
                        [
                            'year' => '2026',
                            'title' => 'Premium Ecommerce Platform',
                            'text' => 'MyBestStore is growing into a complete electronics shopping destination.',
                            'icon' => 'rocket',
                        ],
                    ];
                @endphp
                @foreach ($milestones as $milestone)
                    <li class="story-timeline-item">
                        <div class="story-timeline-marker" aria-hidden="true">
                            <span class="story-timeline-icon">
                                @if ($milestone['icon'] === 'lightbulb')
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 18v-3m0 0a6 6 0 1 0 0-12 6 6 0 0 0 0 12Zm-7.5 7.5h15"/></svg>
                                @elseif ($milestone['icon'] === 'grid')
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6Zm10 0a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2V6ZM4 16a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2Zm10 0a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2v-2Z"/></svg>
                                @elseif ($milestone['icon'] === 'heart')
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 21s-7-4.5-7-10a4 4 0 0 1 7-2 4 4 0 0 1 7 2c0 5.5-7 10-7 10Z"/></svg>
                                @else
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.82m5.84-2.56a11.04 11.04 0 0 0 .59-2.18 6 6 0 0 0-7.02-5.92M12 2v2m0 16v2M4.22 4.22l1.42 1.42m12.72 12.72 1.42 1.42M2 12h2m16 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                                @endif
                            </span>
                        </div>
                        <article class="story-timeline-card">
                            <span class="story-timeline-year">{{ $milestone['year'] }}</span>
                            <h3 class="story-timeline-title">{{ $milestone['title'] }}</h3>
                            <p class="story-timeline-text">{{ $milestone['text'] }}</p>
                        </article>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- 4. Mission & Vision --}}
    <section class="story-mission-vision">
        <div class="mbs-container story-mission-vision-grid">
            <article class="story-mv-card">
                <span class="story-mv-icon story-mv-icon--blue" aria-hidden="true">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 3 4 7v6c0 5 3.5 9.5 8 10 4.5-.5 8-5 8-10V7l-8-4Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="m9 12 2 2 4-4"/></svg>
                </span>
                <h2 class="story-mv-title">Our Mission</h2>
                <p class="story-mv-text">To provide genuine electronics with reliable service, transparent pricing and a smooth shopping experience.</p>
            </article>
            <article class="story-mv-card">
                <span class="story-mv-icon story-mv-icon--green" aria-hidden="true">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941"/></svg>
                </span>
                <h2 class="story-mv-title">Our Vision</h2>
                <p class="story-mv-text">To become Pakistan’s trusted premium ecommerce store for electronics, audio and smart lifestyle products.</p>
            </article>
        </div>
    </section>

    {{-- 5. Values --}}
    <section class="story-values">
        <div class="mbs-container">
            <header class="story-section-header story-section-header--center">
                <p class="story-section-label">What We Stand For</p>
                <h2 class="story-section-title">Our Core Values</h2>
            </header>

            <div class="story-value-grid">
                @php
                    $values = [
                        ['title' => 'Genuine Products', 'desc' => 'Only authentic electronics backed by official brand warranty.', 'icon' => 'shield'],
                        ['title' => 'Customer Trust', 'desc' => 'Honest pricing, clear policies and support you can rely on.', 'icon' => 'trust'],
                        ['title' => 'Fast Delivery', 'desc' => 'Nationwide shipping with secure packaging and timely updates.', 'icon' => 'truck'],
                        ['title' => 'Helpful Support', 'desc' => 'Expert guidance before and after every purchase.', 'icon' => 'chat'],
                    ];
                @endphp
                @foreach ($values as $value)
                    <article class="story-value-card">
                        <span class="story-value-icon" aria-hidden="true">
                            @if ($value['icon'] === 'shield')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                            @elseif ($value['icon'] === 'trust')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                            @elseif ($value['icon'] === 'truck')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8.25 18.75a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3ZM15.75 18.75a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 6h11v9H3V6Zm11 0 3.5 3.5V15H14"/></svg>
                            @else
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8.625 12a3.375 3.375 0 0 1 6.75 0 3.375 3.375 0 0 1-6.75 0Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 2.25c-2.717 0-5.216.568-7.5 1.632V12c0 4.556 3.38 8.25 7.5 8.25s7.5-3.694 7.5-8.25V3.882A16.502 16.502 0 0 0 12 2.25Z"/></svg>
                            @endif
                        </span>
                        <h3 class="story-value-title">{{ $value['title'] }}</h3>
                        <p class="story-value-desc">{{ $value['desc'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 6. CTA --}}
    <section class="story-cta">
        <div class="mbs-container story-cta-inner">
            <h2 class="story-cta-title">Ready to explore premium electronics?</h2>
            <p class="story-cta-text">Discover curated products for your home, office and lifestyle.</p>
            <div class="story-cta-actions">
                <a href="{{ route('shop') }}" class="story-cta-btn story-cta-btn--primary">Shop Now</a>
                <a href="{{ route('contact') }}" class="story-cta-btn story-cta-btn--outline">Contact Us</a>
            </div>
        </div>
    </section>
</div>
@endsection
