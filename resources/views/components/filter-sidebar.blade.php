@php
    $brands = $filterBrands ?? $brands ?? [];
    $categories = $categories ?? [];
    $filters = $filters ?? [];
    $categoryCounts = $categoryCounts ?? [];
    $formId = $formId ?? 'shop-catalog-form';
    $selectedCategories = $filters['categories'] ?? [];
    $selectedBrands = $filters['brands'] ?? [];
    $selectedRating = $filters['rating'] ?? null;
@endphp

<aside class="shop-sidebar-card lg:sticky lg:top-28 lg:h-fit">
    <div class="shop-filter-mobile-header">
        <h3 class="font-bold text-navy">Filters</h3>
        <button type="button" @click="filterOpen = false" class="mbs-icon-btn" aria-label="Close filters">✕</button>
    </div>
    <div class="shop-filter-panel">
        <div class="mbs-filter-help">
            <p class="mbs-filter-help-title">AI Shopping Help</p>
            <p class="mt-2 text-sm text-muted">Get recommendations based on your room size, budget and usage.</p>
            <button type="button" @click="$root.searchOpen = true" class="mbs-btn mbs-btn-outline w-full">
                Get Suggestions
            </button>
        </div>

        <div class="mbs-filter-scroll space-y-6 p-5">
            <div>
                <h3 class="mbs-filter-title">Search</h3>
                <input
                    type="search"
                    name="search"
                    value="{{ $filters['search'] ?? '' }}"
                    placeholder="Search products..."
                    class="mbs-input mt-3 text-sm"
                    @keydown.enter.prevent="$el.form.requestSubmit()"
                >
            </div>

            <div>
                <h3 class="mbs-filter-title">Categories</h3>
                <ul class="mt-3 space-y-2.5 text-sm text-muted">
                    @foreach (array_slice($categories, 0, 12) as $category)
                        @php
                            $slug = $category['slug'] ?? '';
                            $count = $categoryCounts[$slug] ?? ($category['count'] ?? 0);
                        @endphp
                        <li>
                            <label class="flex cursor-pointer items-center justify-between gap-2 hover:text-primary">
                                <span class="flex items-center gap-2">
                                    <input
                                        type="checkbox"
                                        name="categories[]"
                                        value="{{ $slug }}"
                                        @checked(in_array($slug, $selectedCategories, true))
                                        class="rounded border-border text-primary focus:ring-primary"
                                    >
                                    {{ $category['name'] }}
                                </span>
                                <span class="text-xs text-muted">{{ $count }}</span>
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h3 class="mbs-filter-title">Price Range</h3>
                <div class="mt-3 grid grid-cols-2 gap-2">
                    <input
                        type="number"
                        name="min_price"
                        value="{{ $filters['min_price'] ?? '' }}"
                        min="0"
                        step="1"
                        placeholder="Min"
                        class="mbs-input text-sm"
                    >
                    <input
                        type="number"
                        name="max_price"
                        value="{{ $filters['max_price'] ?? '' }}"
                        min="0"
                        step="1"
                        placeholder="Max"
                        class="mbs-input text-sm"
                    >
                </div>
            </div>

            <div>
                <h3 class="mbs-filter-title">Brand</h3>
                <ul class="mt-3 max-h-44 space-y-2.5 overflow-y-auto text-sm text-muted">
                    @foreach ($brands as $brand)
                        @php $brandName = is_array($brand) ? ($brand['name'] ?? '') : $brand; @endphp
                        <li>
                            <label class="flex cursor-pointer items-center gap-2 hover:text-primary">
                                <input
                                    type="checkbox"
                                    name="brand[]"
                                    value="{{ $brandName }}"
                                    @checked(in_array($brandName, $selectedBrands, true))
                                    class="rounded border-border text-primary focus:ring-primary"
                                >
                                {{ $brandName }}
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h3 class="mbs-filter-title">Rating</h3>
                <ul class="mt-3 space-y-2.5 text-sm text-muted">
                    @foreach ([4, 3, 2] as $stars)
                        <li>
                            <label class="flex cursor-pointer items-center gap-2 hover:text-primary">
                                <input
                                    type="radio"
                                    name="rating"
                                    value="{{ $stars }}"
                                    @checked((int) $selectedRating === $stars)
                                    class="rounded-full border-border text-primary focus:ring-primary"
                                >
                                {{ $stars }}+ Stars
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h3 class="mbs-filter-title">Product Type</h3>
                <ul class="mt-3 space-y-2.5 text-sm text-muted">
                    @if (!empty($showFeaturedFilter))
                        <li>
                            <label class="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    name="featured"
                                    value="1"
                                    @checked(!empty($filters['featured']))
                                    class="rounded border-border text-primary focus:ring-primary"
                                >
                                Featured
                            </label>
                        </li>
                    @endif
                    @if (!empty($showNewArrivalsFilter))
                        <li>
                            <label class="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    name="new_arrivals"
                                    value="1"
                                    @checked(!empty($filters['new_arrivals']))
                                    class="rounded border-border text-primary focus:ring-primary"
                                >
                                New Arrivals
                            </label>
                        </li>
                    @endif
                    <li>
                        <label class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                name="in_stock"
                                value="1"
                                @checked(!empty($filters['in_stock']))
                                class="rounded border-border text-primary focus:ring-primary"
                            >
                            In Stock
                        </label>
                    </li>
                    <li>
                        <label class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                name="on_sale"
                                value="1"
                                @checked(!empty($filters['on_sale']))
                                class="rounded border-border text-primary focus:ring-primary"
                            >
                            On Sale
                        </label>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-border p-4">
            <button type="submit" class="mbs-btn mbs-btn-primary w-full">Apply Filters</button>
            <a href="{{ $clearFiltersUrl ?? route('shop') }}" class="mbs-btn mbs-btn-outline mt-2 w-full text-center">Clear Filters</a>
        </div>
    </div>
</aside>
