@php
    use App\Support\Mbs;
    $category = $category ?? [];
    $name = $category['name'] ?? 'Category';
    $count = $category['count'] ?? 0;
    $image = Mbs::image($category['image'] ?? 'placeholder-product.svg');
    $description = $category['description'] ?? '';
    $large = $large ?? false;
@endphp

<article class="mbs-category-card group">
    <div @class(['mbs-category-card-media', 'mbs-category-card-media--large' => $large])>
        <img src="{{ $image }}" alt="{{ $category['image_alt'] ?? $name }}" loading="lazy">
        <div class="mbs-category-card-overlay"></div>
        @if ($large)
            <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                <h3 class="text-lg font-bold">{{ $name }}</h3>
                <p class="mt-1 line-clamp-2 text-xs text-blue-100">{{ $description }}</p>
            </div>
        @endif
    </div>
    <div class="mbs-category-card-body">
        @if (! $large)
            <h3 class="font-bold text-navy">{{ $name }}</h3>
            <p class="mt-1 line-clamp-2 text-sm text-muted">{{ $description }}</p>
        @endif
        <div class="mt-3 flex items-center justify-between gap-3">
            <p class="text-sm text-muted">{{ $count }} products</p>
            <a href="{{ Mbs::shopCategoryUrl($category['slug'] ?? '') }}" class="mbs-section-link">
                Explore <span>→</span>
            </a>
        </div>
    </div>
</article>
