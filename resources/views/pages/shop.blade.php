@extends('layouts.app')

@section('title', 'MyBestStore | Shop')

@section('content')
@include('components.page-hero', [
    'title' => 'Shop All Products',
    'description' => 'Browse premium electronics, audio, appliances and more.',
])

<section class="mbs-page-section mbs-page-section--muted">
    <div class="mbs-container">
        <div x-show="filterOpen" x-cloak class="fixed inset-0 z-50 bg-slate-900/50 p-4 lg:hidden">
            <div class="ml-auto h-full max-h-full w-full max-w-sm overflow-y-auto rounded-2xl bg-white">
                <div class="flex items-center justify-between border-b border-border p-4">
                    <h3 class="font-bold text-navy">Filters</h3>
                    <button type="button" @click="filterOpen = false" class="mbs-icon-btn">✕</button>
                </div>
                @include('components.filter-sidebar')
            </div>
        </div>

        <div class="mbs-shop-layout">
            <div class="hidden lg:block">
                @include('components.filter-sidebar')
            </div>
            <div>
                @include('components.shop-toolbar', [
                    'total' => $products->total(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                    'showFilterButton' => true,
                ])
                <div class="mbs-product-grid mbs-product-grid--4">
                    @forelse ($products as $product)
                        @include('components.product-card', ['product' => $product])
                    @empty
                        <p class="mbs-empty-state">No products found.</p>
                    @endforelse
                </div>
                @include('components.pagination', ['paginator' => $products])
            </div>
        </div>
    </div>
</section>
@endsection
