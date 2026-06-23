@extends('cms::layouts.admin')

@section('title', $title)

@section('page_heading')
<div class="sf-page-head">
    <div>
        <h1 class="sf-page-title">{{ $title }}</h1>
        <p class="sf-page-subtitle">Curate products shown in the {{ strtolower($title) }} section on the storefront.</p>
    </div>
    <div class="sf-actions">
        <a href="{{ route('cms.merchandising.featured') }}" @class(['sf-tab', 'is-active' => $type === 'featured'])>Featured</a>
        <a href="{{ route('cms.merchandising.new-arrivals') }}" @class(['sf-tab', 'is-active' => $type === 'new-arrivals'])>New Arrivals</a>
    </div>
</div>
@endsection

@section('content')
@if ($canEdit)
    <div class="sf-panel">
        <h2 style="margin:0 0 16px;font-size:1.05rem;font-weight:700;">Add Product</h2>
        <form method="POST" action="{{ route('cms.merchandising.store', $type) }}" class="cms-form-inline" style="display:flex;flex-wrap:wrap;gap:16px;align-items:end;">
            @csrf
            <label style="flex:1;min-width:220px;">
                Product
                <select name="product_id" required style="width:100%;padding:10px;border:1px solid var(--sf-border);border-radius:8px;">
                    <option value="">Select product</option>
                    @foreach ($availableProducts as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} (#{{ $product->id }})</option>
                    @endforeach
                </select>
            </label>
            <label>
                Sort order
                <input type="number" name="sort_order" value="0" min="0" style="width:100px;padding:10px;border:1px solid var(--sf-border);border-radius:8px;">
            </label>
            <button type="submit" class="sf-btn sf-btn-primary">Add</button>
        </form>
    </div>
@endif

<div class="sf-panel sf-panel--flush">
    <div class="sf-table-wrap">
        <table class="sf-table">
            <thead>
                <tr>
                    <th>Sort</th>
                    <th>Product</th>
                    <th>Slug</th>
                    @if ($canEdit)<th>Actions</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->sort_order }}</td>
                        <td><strong>{{ $item->product?->name ?? 'Product #'.$item->product_id }}</strong></td>
                        <td>{{ $item->product?->slug }}</td>
                        @if ($canEdit)
                            <td class="sf-actions">
                                <form method="POST" action="{{ route('cms.merchandising.destroy', [$type, $item->id]) }}" onsubmit="return confirm('Remove this product from the list?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="sf-action sf-action--delete">Remove</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $canEdit ? 4 : 3 }}" class="sf-empty">No products in this list yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
