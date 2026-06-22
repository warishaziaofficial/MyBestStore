@props([
    'total' => 0,
    'showFilterButton' => false,
    'label' => 'products',
    'from' => null,
    'to' => null,
    'enableViewToggle' => false,
    'sortOptions' => [],
    'filters' => [],
    'formId' => 'shop-catalog-form',
])

@php
    $sortOptions = $sortOptions ?: \App\Services\ProductCatalogService::SORT_OPTIONS;
    $currentSort = $filters['sort'] ?? 'featured';
@endphp

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
        <select
            name="sort"
            class="mbs-input w-auto py-2 text-sm"
            @change="$el.form.requestSubmit()"
        >
            @foreach ($sortOptions as $value => $labelText)
                <option value="{{ $value }}" @selected($currentSort === $value)>Sort: {{ $labelText }}</option>
            @endforeach
        </select>
        <div class="mbs-view-toggle">
            @if ($enableViewToggle)
                <button type="button" @click="setView('grid')" :class="{ 'is-active': shopView === 'grid' }">Grid</button>
                <button type="button" @click="setView('list')" :class="{ 'is-active': shopView === 'list' }">List</button>
            @else
                <button type="button" class="is-active">Grid</button>
                <button type="button">List</button>
            @endif
        </div>
    </div>
</div>
