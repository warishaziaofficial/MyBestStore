@extends('cms::layouts.admin')

@section('title', 'Products')

@section('page_heading')
<div class="sf-page-head">
    <h1 class="sf-page-title">Products</h1>
    @if ($canEdit)
        <a href="{{ route('cms.resource.create', 'products') }}" class="sf-btn sf-btn-primary">+ Add Product</a>
    @endif
</div>
@endsection

@section('content')
<form method="GET" action="{{ route('cms.products.index') }}" class="sf-search">
    <input type="search" name="search" value="{{ $search }}" placeholder="Search products...">
</form>

<div class="sf-panel sf-panel--flush">
    <div class="sf-table-wrap">
        <table class="sf-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Featured</th>
                    @if ($canEdit)
                        <th>Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>
                            <div class="sf-product-name">
                                @if ($product->image)
                                    <img src="{{ asset($product->image) }}" alt="" class="sf-product-thumb">
                                @endif
                                <strong>{{ $product->name }}</strong>
                            </div>
                        </td>
                        <td>{{ $product->category }}</td>
                        <td>Rs {{ number_format($product->price) }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>
                            @if ($product->featured)
                                <span class="sf-pill sf-pill--green">Yes</span>
                            @else
                                <span class="sf-pill sf-pill--gray">No</span>
                            @endif
                        </td>
                        @if ($canEdit)
                            <td class="sf-actions">
                                <a href="{{ route('cms.resource.edit', ['products', $product->id]) }}" class="sf-action sf-action--edit">Edit</a>
                                <form method="POST" action="{{ route('cms.resource.destroy', ['products', $product->id]) }}" onsubmit="return confirm('Delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="sf-action sf-action--delete">Delete</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $canEdit ? 6 : 5 }}" class="sf-empty">
                            No products found.
                            @if ($canEdit)
                                <a href="{{ route('cms.resource.create', 'products') }}">Add your first product</a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
