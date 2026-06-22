@php
    use App\Support\StorefrontData;
    $featured = array_slice(StorefrontData::allProducts(), 0, 3);
    $popularTerms = ['TV', 'Samsung', 'Soundbar', 'Air Purifier'];
@endphp

<div
    x-show="searchOpen"
    x-cloak
    class="search-modal-overlay"
    @keydown.escape.window="searchOpen = false"
>
    <div class="search-modal" @click.outside="searchOpen = false" role="dialog" aria-modal="true" aria-label="Search">
        <button type="button" class="search-modal-close" @click="searchOpen = false" aria-label="Close search">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
            </svg>
        </button>

        <div class="search-modal-body">
            <div class="search-modal-input-wrap">
                <input
                    type="search"
                    placeholder="Search"
                    class="search-modal-input"
                    x-model="searchQuery"
                    @input.debounce.350ms="runSearch()"
                    autofocus
                >
                <svg class="search-modal-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="11" cy="11" r="7" stroke-width="2"/>
                    <path stroke-linecap="round" stroke-width="2" d="m20 20-3.5-3.5"/>
                </svg>
            </div>

            <p class="search-popular-inline">
                <span class="search-popular-label">Popular searches:</span>
                @foreach ($popularTerms as $term)
                    <button type="button" class="search-popular-link" @click="setPopularSearch(@js($term))">{{ $term }}</button>
                @endforeach
            </p>

            <h2 class="search-featured-title" x-text="searchQuery.trim() ? 'Search Results' : 'Featured Products'"></h2>

            <p class="search-modal-status" x-show="searchLoading">Searching...</p>
            <p class="search-modal-empty" x-show="!searchLoading && searchQuery.trim() && searchEmpty" x-cloak>
                No products found for your search.
            </p>

            <div class="search-modal-results" x-show="!searchLoading && !(searchQuery.trim() && searchEmpty)">
                <div x-show="!searchQuery.trim()">
                    @include('components.search-results-grid', ['products' => $featured])
                </div>
                <div x-show="searchQuery.trim()" x-cloak x-ref="searchResults" x-html="searchHtml"></div>
            </div>
        </div>
    </div>
</div>
