@props([
    'title',
    'subtitle' => null,
    'products' => [],
    'gridClass' => 'mbs-product-grid--4',
])

@if (count($products))
<section class="mbs-page-section bg-white product-relations-section">
    <div class="mbs-container">
        @include('components.section-header', [
            'title' => $title,
            'subtitle' => $subtitle,
        ])
        <div @class(['mbs-product-grid', $gridClass])>
            @foreach ($products as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif
