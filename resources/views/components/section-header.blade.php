@props([
    'title',
    'subtitle' => null,
    'viewAllHref' => null,
    'viewAllLabel' => 'View All',
    'centered' => false,
])

<div @class(['mbs-section-header', 'mbs-section-header--center' => $centered])>
    <div class="max-w-2xl">
        <h2 class="mbs-section-title">{{ $title }}</h2>
        @if ($subtitle)
            <p class="mbs-section-subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    @if ($viewAllHref)
        <a href="{{ $viewAllHref }}" class="mbs-section-link">{{ $viewAllLabel }} →</a>
    @endif
</div>
