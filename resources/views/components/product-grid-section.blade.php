@props([
    'title',
    'products' => [],
    'viewAllHref' => null,
    'bg' => 'bg-secondary',
    'grid' => '4',
    'subtitle' => null,
])

<section @class([$bg, 'home-section'])>
    <div class="mbs-container">
        @include('components.section-header', [
            'title' => $title,
            'subtitle' => $subtitle,
            'viewAllHref' => $viewAllHref,
        ])
        <div @class([
            'home-section-inner mbs-product-grid',
            'mbs-product-grid--4' => $grid === '4',
            'mbs-product-grid--3' => $grid === '3',
        ])>
            @foreach ($products as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
