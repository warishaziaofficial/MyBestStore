@extends('layouts.app')

@section('title', 'MyBestStore | Shop')

@section('content')
@include('components.page-hero', [
    'title' => $categoryMeta['name'] ?? 'Shop All Products',
    'description' => $categoryMeta['description'] ?? 'Browse premium electronics, audio, appliances and more.',
])

@include('components.shop-catalog', [
    'products' => $products,
    'filters' => $filters,
    'catalogAction' => $catalogAction,
    'catalogContext' => $catalogContext,
    'sortOptions' => $sortOptions,
    'categoryCounts' => $categoryCounts,
    'clearFiltersUrl' => $clearFiltersUrl,
    'showNewArrivalsFilter' => $showNewArrivalsFilter ?? true,
    'showFeaturedFilter' => $showFeaturedFilter ?? true,
    'label' => 'products',
])
@endsection
