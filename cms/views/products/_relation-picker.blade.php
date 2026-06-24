@php
    $selected = old('relations.'.$type, $selectedRelations[$type] ?? []);
    $selected = array_map('intval', (array) $selected);
    $currentId = $item ? (int) data_get($item, 'id') : null;
    $pickerProducts = $allProducts->filter(fn ($product) => (int) $product->id !== $currentId)->values();
    $productOptions = $pickerProducts->map(fn ($product) => [
        'id' => (int) $product->id,
        'name' => $product->name,
    ])->values();
@endphp

<div
    class="sf-ms"
    data-product-multiselect
    data-field-name="relations[{{ $type }}]"
    data-products='@json($productOptions)'
    data-selected='@json(array_values($selected))'
>
    <label class="sf-form-label">{{ $label }}</label>

    @if ($pickerProducts->isEmpty())
        <p class="sf-form-empty">No other products yet. Add more products first.</p>
    @else
        <div class="sf-ms-field" data-ms-field tabindex="0" role="combobox" aria-expanded="false" aria-haspopup="listbox">
            <div class="sf-ms-tags" data-ms-tags></div>
            <input
                type="text"
                class="sf-ms-input"
                data-ms-input
                placeholder="Search and select products…"
                autocomplete="off"
                aria-autocomplete="list"
            >
            <span class="sf-ms-chevron" aria-hidden="true">▾</span>
        </div>
        <div class="sf-ms-menu" data-ms-menu hidden role="listbox"></div>
        <div class="sf-ms-hidden" data-ms-hidden></div>
    @endif
</div>
