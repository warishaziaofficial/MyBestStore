@extends('layouts.app')



@section('title', 'MyBestStore | Wishlist')



@section('content')

@include('components.page-hero', [

    'title' => 'My Wishlist',

    'description' => 'Products you saved for later.',

])



<section class="mbs-page-section mbs-page-section--muted">

    <div class="mbs-container">

        <div class="wishlist-empty" x-show="wishlistSlugs.length === 0" x-cloak>

            <h2>Your wishlist is empty.</h2>

            <p>Browse the shop and tap the heart icon to save products you love.</p>

            <a href="{{ route('shop') }}" class="mbs-btn mbs-btn-primary">Continue Shopping</a>

        </div>



        @if (!empty($products))

            <div class="mbs-product-grid mbs-product-grid--4" x-show="wishlistSlugs.length > 0">

                @foreach ($products as $product)

                    <div class="wishlist-item-wrap" x-show="isWishlisted(@js($product['slug']))" x-transition>

                        @include('components.product-card', ['product' => $product])

                        <button type="button" class="wishlist-remove-btn" @click="toggleWishlist(@js($product['slug']))">Remove from wishlist</button>

                    </div>

                @endforeach

            </div>

        @endif

    </div>

</section>

@endsection

