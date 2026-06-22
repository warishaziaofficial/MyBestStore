@extends('layouts.app')

@section('title', 'MyBestStore | New Arrivals')

@section('content')
@include('components.page-hero', [
    'title' => 'New Arrivals',
    'description' => 'Discover the latest products recently added to MyBestStore.',
])

@include('components.shop-catalog', [
    'products' => $products,
    'filters' => $filters,
    'catalogAction' => $catalogAction,
    'catalogContext' => $catalogContext,
    'sortOptions' => $sortOptions,
    'categoryCounts' => $categoryCounts,
    'clearFiltersUrl' => $clearFiltersUrl,
    'showNewArrivalsFilter' => $showNewArrivalsFilter ?? false,
    'showFeaturedFilter' => $showFeaturedFilter ?? true,
    'label' => 'new arrivals',
    'emptyMessage' => 'No products found. Try changing your filters.',
])
@endsection
