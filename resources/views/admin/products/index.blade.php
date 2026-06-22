@extends('layouts.admin')

@section('title', 'Products')
@section('heading', 'Product Catalog — Relations')

@section('content')
<div class="cms-card">
    <form method="GET" action="{{ route('admin.products.index') }}" class="cms-search">
        <input type="search" name="q" value="{{ $query }}" class="mbs-input" placeholder="Search products by name, slug, category...">
        <button type="submit" class="mbs-btn mbs-btn-primary">Search</button>
    </form>

    <table class="cms-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>
                        <strong>{{ $product['name'] }}</strong>
                        <br><small class="text-muted">{{ $product['slug'] }}</small>
                    </td>
                    <td>{{ $product['category_name'] ?? $product['category'] }}</td>
                    <td>{{ number_format($product['price']) }} PKR</td>
                    <td>
                        <a href="{{ route('admin.products.edit', $product['slug']) }}" class="cms-link">Edit relations</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No products found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
