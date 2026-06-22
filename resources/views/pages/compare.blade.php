@extends('layouts.app')
@php use App\Support\Mbs; @endphp

@section('title', 'MyBestStore | Compare Products')

@section('content')
@include('components.page-hero', [
    'title' => 'Compare Products',
    'description' => 'Review specs and prices side by side before you buy.',
])

<section class="mbs-page-section mbs-page-section--muted compare-page">
    <div class="mbs-container">
        <div class="compare-empty" x-show="compareSlugs.length === 0" x-cloak>
            <h2>No products to compare yet.</h2>
            <p>Click the compare icon on any product card to add it here.</p>
            <a href="{{ route('shop') }}" class="mbs-btn mbs-btn-primary">Browse Shop</a>
        </div>

        @if (!empty($products))
            <div class="compare-table-wrap" x-show="compareSlugs.length > 0" x-cloak>
                <div class="compare-table-scroll">
                    <table class="compare-table">
                        <thead>
                            <tr>
                                <th scope="col">Feature</th>
                                @foreach ($products as $product)
                                    <th scope="col" x-show="isCompared(@js($product['slug']))">
                                        <div class="compare-product-head">
                                            <a href="{{ route('product.show', $product['slug']) }}" class="compare-product-image">
                                                <img src="{{ Mbs::image($product['image']) }}" alt="{{ $product['name'] }}">
                                            </a>
                                            <a href="{{ route('product.show', $product['slug']) }}" class="compare-product-name">{{ $product['name'] }}</a>
                                            <button type="button" class="compare-remove-btn" @click="toggleCompare(@js($product['slug']))">Remove</button>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">Price</th>
                                @foreach ($products as $product)
                                    <td x-show="isCompared(@js($product['slug']))">{{ Mbs::price((int) $product['price']) }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <th scope="row">Brand</th>
                                @foreach ($products as $product)
                                    <td x-show="isCompared(@js($product['slug']))">{{ $product['brand'] ?? 'MyBestStore' }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <th scope="row">Category</th>
                                @foreach ($products as $product)
                                    <td x-show="isCompared(@js($product['slug']))">{{ ucwords(str_replace('-', ' ', $product['category'] ?? 'General')) }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <th scope="row">Rating</th>
                                @foreach ($products as $product)
                                    <td x-show="isCompared(@js($product['slug']))">{{ number_format((float) ($product['rating'] ?? 0), 1) }} / 5</td>
                                @endforeach
                            </tr>
                            <tr>
                                <th scope="row">Availability</th>
                                @foreach ($products as $product)
                                    <td x-show="isCompared(@js($product['slug']))">{{ !empty($product['in_stock']) ? 'In stock' : 'Out of stock' }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <th scope="row">Action</th>
                                @foreach ($products as $product)
                                    <td x-show="isCompared(@js($product['slug']))">
                                        <a href="{{ route('product.show', $product['slug']) }}" class="compare-view-btn">View Product</a>
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="compare-note">You can compare up to {{ $maxItems }} products at a time.</p>
            </div>
        @endif
    </div>
</section>
@endsection
