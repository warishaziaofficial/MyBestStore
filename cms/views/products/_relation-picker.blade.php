@php
    $selected = old('relations.'.$type, $selectedRelations[$type] ?? []);
    $currentId = $item ? (int) data_get($item, 'id') : null;
    $pickerProducts = $allProducts->filter(fn ($product) => (int) $product->id !== $currentId)->values();
@endphp

<div class="sf-relation-picker" data-relation-picker>
    <label class="sf-form-label">{{ $label }}</label>

    @if ($pickerProducts->isEmpty())
        <p class="sf-form-empty">No other products yet. Save this product first, then add more products to set {{ strtolower($label) }}.</p>
    @else
        <input
            type="search"
            class="sf-input sf-relation-search"
            placeholder="Search products by name…"
            autocomplete="off"
            data-relation-search
        >
        <div class="sf-relation-list" data-relation-list>
            @foreach ($pickerProducts as $product)
                <label class="sf-relation-item" data-relation-item data-name="{{ strtolower($product->name) }}">
                    <input
                        type="checkbox"
                        name="relations[{{ $type }}][]"
                        value="{{ $product->id }}"
                        @checked(in_array($product->id, $selected))
                    >
                    <span class="sf-relation-item-text">
                        <strong>{{ $product->name }}</strong>
                        <span>#{{ $product->id }}</span>
                    </span>
                </label>
            @endforeach
        </div>
        <p class="sf-form-hint">Search above, then tick the products you want. Click again to remove.</p>
    @endif
</div>
