@props([
    'title',
    'products' => [],
    'viewAllHref' => null,
    'bg' => 'bg-secondary',
    'subtitle' => null,
    'showHeader' => true,
])

<section @class([$bg, 'home-section', 'home-section--cards-only' => ! $showHeader])>
    <div class="mbs-container">
        @if ($showHeader)
            @include('components.section-header', [
                'title' => $title,
                'subtitle' => $subtitle,
                'viewAllHref' => $viewAllHref,
            ])
        @endif
        <div @class(['home-section-inner mbs-product-grid mbs-product-grid--4', 'home-section-inner--flush' => ! $showHeader])>
            @foreach ($products as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
