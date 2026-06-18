@php
    use App\Support\Mbs;
    $category = $category ?? [];
    $name = $category['name'] ?? 'Category';
    $image = Mbs::image($category['image'] ?? 'placeholder-product.svg');
@endphp

<article class="category-card mbs-shop-category-tile group">
    <div class="category-image-wrap">
        <a href="{{ route('shop') }}" class="category-image-link">
            <img
                src="{{ $image }}"
                alt="{{ $category['image_alt'] ?? $name }}"
                class="category-image"
                loading="lazy"
            >
        </a>
    </div>
    <div class="category-card-body">
        <h3 class="category-title">{{ $name }}</h3>
        <a
            class="category-arrow"
            href="{{ route('shop') }}"
            aria-label="Explore {{ $name }}"
        >
            <svg class="category-arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</article>
