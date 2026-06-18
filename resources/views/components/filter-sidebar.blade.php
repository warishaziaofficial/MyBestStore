@php
    $brands = $filterBrands ?? $brands ?? [];
    $categories = $categories ?? [];
@endphp

<aside class="shop-sidebar-card lg:sticky lg:top-28 lg:h-fit">
    <div class="shop-filter-panel">
        <div class="mbs-filter-help">
            <p class="mbs-filter-help-title">AI Shopping Help</p>
            <p class="mt-2 text-sm text-muted">Get recommendations based on your room size, budget and usage.</p>
            <button type="button" @click="searchOpen = true" class="mbs-btn mbs-btn-outline w-full">
                Get Suggestions
            </button>
        </div>

        <div class="mbs-filter-scroll space-y-6 p-5">
            <div>
                <h3 class="mbs-filter-title">Search</h3>
                <input type="search" placeholder="Search products..." class="mbs-input mt-3 text-sm">
            </div>

            <div>
                <h3 class="mbs-filter-title">Categories</h3>
                <ul class="mt-3 space-y-2.5 text-sm text-muted">
                    @foreach (array_slice($categories, 0, 10) as $category)
                        <li>
                            <label class="flex cursor-pointer items-center justify-between gap-2 hover:text-primary">
                                <span class="flex items-center gap-2">
                                    <input type="checkbox" class="rounded border-border text-primary focus:ring-primary">
                                    {{ $category['name'] }}
                                </span>
                                <span class="text-xs text-muted">{{ $category['count'] ?? 0 }}</span>
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h3 class="mbs-filter-title">Price Range</h3>
                <div class="mt-3 grid grid-cols-2 gap-2">
                    <input type="number" placeholder="Min" class="mbs-input text-sm">
                    <input type="number" placeholder="Max" class="mbs-input text-sm">
                </div>
            </div>

            <div>
                <h3 class="mbs-filter-title">Brand</h3>
                <ul class="mt-3 max-h-44 space-y-2.5 overflow-y-auto text-sm text-muted">
                    @foreach ($brands as $brand)
                        <li>
                            <label class="flex cursor-pointer items-center gap-2 hover:text-primary">
                                <input type="checkbox" class="rounded border-border text-primary focus:ring-primary">
                                {{ is_array($brand) ? $brand['name'] : $brand }}
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
                                <input type="checkbox" class="rounded border-border text-primary focus:ring-primary">
                                {{ $stars }}+ Stars
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h3 class="mbs-filter-title">Availability</h3>
                <ul class="mt-3 space-y-2.5 text-sm text-muted">
                    <li><label class="flex items-center gap-2"><input type="checkbox" class="rounded border-border text-primary"> In Stock</label></li>
                    <li><label class="flex items-center gap-2"><input type="checkbox" class="rounded border-border text-primary"> On Sale</label></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-border p-4">
            <button type="button" class="mbs-btn mbs-btn-primary w-full">Apply Filters</button>
            <button type="button" class="mbs-btn mbs-btn-outline w-full">Reset</button>
        </div>
    </div>
</aside>
