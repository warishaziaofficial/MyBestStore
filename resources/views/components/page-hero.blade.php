@props([
    'title',
    'breadcrumb' => null,
    'description' => null,
])

<section class="mbs-page-hero">
    <div class="mbs-container mbs-page-hero-inner">
        @if ($breadcrumb)
            <nav class="mbs-breadcrumb">{!! $breadcrumb !!}</nav>
        @else
            <nav class="mbs-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="transition-colors hover:text-primary">Home</a>
                <span>/</span>
                <span class="font-medium text-navy">{{ $title }}</span>
            </nav>
        @endif
        <h1 class="mbs-page-title">{{ $title }}</h1>
        @if ($description)
            <p class="mbs-section-subtitle">{{ $description }}</p>
        @endif
    </div>
</section>
