@extends('layouts.admin')

@section('title', 'Edit Product Relations')
@section('heading', 'Edit Product')

@section('content')
<div class="cms-card">
    <p><strong>{{ $product['name'] }}</strong> · {{ $product['category_name'] ?? $product['category'] }} · {{ number_format($product['price']) }} PKR</p>
    <p><a href="{{ route('product.show', $product['slug']) }}" class="cms-link" target="_blank" rel="noopener">View on storefront</a></p>

    <form method="POST" action="{{ route('admin.products.update', $product['slug']) }}" class="mt-4">
        @csrf
        @method('PUT')

        <div class="cms-field">
            <label for="sku">SKU</label>
            <input id="sku" type="text" name="sku" value="{{ old('sku', $dbProduct->sku ?? '') }}" class="mbs-input" placeholder="Product SKU">
        </div>

        <div class="cms-field">
            <label for="barcode">Product Barcode</label>
            <input id="barcode" type="text" name="barcode" value="{{ old('barcode', $dbProduct->barcode ?? '') }}" class="mbs-input" placeholder="Leave empty to auto-generate">
            @if (! empty($dbProduct?->barcode))
                <small>Current barcode: {{ $dbProduct->barcode }}</small>
            @endif
        </div>

        <div class="cms-field">
            <label for="upsell">Upsell Products</label>
            <select id="upsell" name="upsell[]" class="cms-multiselect" multiple>
                @foreach ($allProducts as $option)
                    @if ($option['slug'] !== $product['slug'])
                        <option value="{{ $option['slug'] }}" @selected(in_array($option['slug'], $selectedUpsell, true))>
                            {{ $option['name'] }} — {{ number_format($option['price']) }} PKR
                        </option>
                    @endif
                @endforeach
            </select>
            <small class="text-muted">Premium upgrades shown on product detail page.</small>
        </div>

        <div class="cms-field">
            <label for="cross_sell">Cross-sell Products</label>
            <select id="cross_sell" name="cross_sell[]" class="cms-multiselect" multiple>
                @foreach ($allProducts as $option)
                    @if ($option['slug'] !== $product['slug'])
                        <option value="{{ $option['slug'] }}" @selected(in_array($option['slug'], $selectedCrossSell, true))>
                            {{ $option['name'] }} — {{ number_format($option['price']) }} PKR
                        </option>
                    @endif
                @endforeach
            </select>
            <small class="text-muted">Complementary items shown on cart page.</small>
        </div>

        <div class="cms-field">
            <label for="related">Related Products</label>
            <select id="related" name="related[]" class="cms-multiselect" multiple>
                @foreach ($allProducts as $option)
                    @if ($option['slug'] !== $product['slug'])
                        <option value="{{ $option['slug'] }}" @selected(in_array($option['slug'], $selectedRelated, true))>
                            {{ $option['name'] }} — {{ number_format($option['price']) }} PKR
                        </option>
                    @endif
                @endforeach
            </select>
            <small class="text-muted">Related picks on product detail page.</small>
        </div>

        <div class="cms-actions">
            <button type="submit" class="mbs-btn mbs-btn-primary">Save Product</button>
            <a href="{{ route('admin.products.index') }}" class="cms-link">Back to list</a>
        </div>
    </form>
</div>
@endsection
