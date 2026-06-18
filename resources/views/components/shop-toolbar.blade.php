@props(['total' => 0, 'showFilterButton' => false, 'label' => 'products', 'from' => null, 'to' => null])

<div class="mbs-toolbar">
    <p class="mbs-toolbar-meta">
        @if ($from && $to)
            Showing <strong>{{ $from }}–{{ $to }}</strong> of <strong>{{ $total }}</strong> {{ $label }}
        @else
            Showing <strong>{{ $total }}</strong> {{ $label }}
        @endif
    </p>
    <div class="mbs-toolbar-actions">
        @if ($showFilterButton)
            <button type="button" @click="filterOpen = true" class="mbs-btn mbs-btn-outline py-2 lg:hidden">Filters</button>
        @endif
        <select class="mbs-input w-auto py-2 text-sm">
            <option>Sort: Featured</option>
            <option>Newest</option>
            <option>Price: Low to High</option>
            <option>Price: High to Low</option>
            <option>Top Rated</option>
        </select>
        <div class="mbs-view-toggle">
            <button type="button" class="is-active">Grid</button>
            <button type="button">List</button>
        </div>
    </div>
</div>
