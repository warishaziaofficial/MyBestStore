@props([
    'products',
    'filters' => [],
    'catalogAction' => route('shop'),
    'catalogContext' => 'shop',
    'sortOptions' => [],
    'categoryCounts' => [],
    'clearFiltersUrl' => route('shop'),
    'showFilterButton' => true,
    'enableViewToggle' => true,
    'showNewArrivalsFilter' => true,
    'showFeaturedFilter' => true,
    'emptyMessage' => 'No products found. Try changing your filters.',
    'label' => 'products',
])

@php
    $filters = $filters ?? [];
    $sortOptions = $sortOptions ?: \App\Services\ProductCatalogService::SORT_OPTIONS;
    $formId = 'shop-catalog-form-' . $catalogContext;
@endphp

<section
    class="mbs-page-section mbs-page-section--muted shop-page"
    x-data="mbsShopCatalog(@js([
        'view' => $filters['view'] ?? 'grid',
        'clearUrl' => $clearFiltersUrl,
    ]))"
>
    <div class="mbs-container">
        <form
            id="{{ $formId }}"
            method="GET"
            action="{{ $catalogAction }}"
            class="shop-catalog-form"
            @submit="onFilterSubmit"
        >
            <input type="hidden" name="view" :value="shopView">

            <div class="mbs-shop-layout">
                <div class="shop-filter-host">
                    <div
                        class="shop-filter-drawer-backdrop"
                        x-show="filterOpen"
                        x-cloak
                        @click="filterOpen = false"
                    ></div>
                    <div class="shop-filter-panel-wrapper" :class="{ 'is-open': filterOpen }">
                        @include('components.filter-sidebar', [
                            'filters' => $filters,
                            'categoryCounts' => $categoryCounts,
                            'showNewArrivalsFilter' => $showNewArrivalsFilter,
                            'showFeaturedFilter' => $showFeaturedFilter,
                            'clearFiltersUrl' => $clearFiltersUrl,
                        ])
                    </div>
                </div>

                <div class="shop-catalog-main">
                    @include('components.shop-toolbar', [
                        'total' => $products->total(),
                        'from' => $products->firstItem(),
                        'to' => $products->lastItem(),
                        'label' => $label,
                        'showFilterButton' => $showFilterButton,
                        'enableViewToggle' => $enableViewToggle,
                        'sortOptions' => $sortOptions,
                        'filters' => $filters,
                    ])

                    <div
                        class="shop-products-grid"
                        :class="shopView === 'list' ? 'shop-products-grid--list' : 'shop-products-grid--grid'"
                    >
                        @forelse ($products as $product)
                            @include('components.product-card', ['product' => $product])
                        @empty
                            <p class="mbs-empty-state">{{ $emptyMessage }}</p>
                        @endforelse
                    </div>

                    @include('components.pagination', ['paginator' => $products])
                </div>
            </div>
        </form>
    </div>
</section>
