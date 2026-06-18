@extends('layouts.app')

@section('title', 'MyBestStore | New Arrivals')

@section('content')
@include('components.page-hero', [
    'title' => 'New Arrivals',
    'description' => 'Discover the latest products recently added to MyBestStore.',
])

<section class="mbs-page-section mbs-page-section--muted">
    <div class="mbs-container">
        @include('components.shop-toolbar', [
            'total' => $products->total(),
            'from' => $products->firstItem(),
            'to' => $products->lastItem(),
            'label' => 'new arrivals',
        ])
        <div class="mbs-product-grid mbs-product-grid--4">
            @forelse ($products as $product)
                @include('components.product-card', ['product' => $product])
            @empty
                <p class="mbs-empty-state">No new arrivals found.</p>
            @endforelse
        </div>
        @include('components.pagination', ['paginator' => $products])
    </div>
</section>
@endsection
