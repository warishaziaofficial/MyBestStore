@extends('layouts.app')
@php
    use App\Support\Mbs;
    use Illuminate\Support\Str;
    $galleryUrls = $galleryUrls ?? [];
@endphp

@section('title', Mbs::storeName().' | '.$product['name'])

@section('content')
@include('components.page-hero', [
    'title' => 'Product Details',
    'description' => $product['category_name'] ?? 'Premium electronics',
])

<section class="mbs-page-section mbs-page-section--muted">
    <div class="mbs-container">
        <div
            class="mbs-product-detail"
            x-data="{
                activeIndex: 0,
                images: @js($galleryUrls),
                qty: 1,
                activeTab: 'description',
                prevImage() { this.activeIndex = (this.activeIndex - 1 + this.images.length) % this.images.length },
                nextImage() { this.activeIndex = (this.activeIndex + 1) % this.images.length },
                setImage(index) { this.activeIndex = index },
                decreaseQty() { if (this.qty > 1) this.qty-- },
                increaseQty() { if (this.qty < 99) this.qty++ },
                shareProduct() {
                    const url = window.location.href;
                    const title = @js($product['name']);

                    if (navigator.share) {
                        navigator.share({ title, url }).catch(() => {});
                        return;
                    }

                    if (navigator.clipboard?.writeText) {
                        navigator.clipboard.writeText(url).then(() => {
                            window.showToast('Product link copied.');
                        });
                        return;
                    }

                    window.prompt('Copy product link:', url);
                }
            }"
        >
            <div class="mbs-product-detail-grid">
                <div class="mbs-product-detail-gallery">
                    <div class="mbs-product-detail-main-wrap">
                        <button type="button" class="mbs-product-detail-arrow mbs-product-detail-arrow--prev" @click="prevImage()" aria-label="Previous image">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <div class="mbs-product-detail-main">
                            <img :src="images[activeIndex]" alt="{{ $product['name'] }}" class="mbs-product-detail-main-img">
                        </div>
                        <button type="button" class="mbs-product-detail-arrow mbs-product-detail-arrow--next" @click="nextImage()" aria-label="Next image">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                    <div class="mbs-product-detail-thumbs">
                        @foreach ($galleryUrls as $index => $url)
                            <button type="button" class="mbs-product-detail-thumb" :class="{ 'is-active': activeIndex === {{ $index }} }" @click="setImage({{ $index }})">
                                <img src="{{ $url }}" alt="">
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="mbs-product-detail-info">
                    <div class="mbs-product-detail-meta">
                        <span>{{ $product['category_name'] }}</span>
                        @if (!empty($product['brand']))
                            <span>{{ $product['brand'] }}</span>
                        @endif
                    </div>

                    <h1 class="mbs-product-detail-title">{{ $product['name'] }}</h1>

                    <div class="mbs-product-detail-rating">
                        @include('components.product-stars', ['rating' => $product['rating']])
                        <span class="mbs-product-detail-rating-value">{{ number_format((float) $product['rating'], 1) }}</span>
                        <span>({{ $product['review_count'] }} {{ Str::plural('review', $product['review_count']) }})</span>
                    </div>

                    <div class="mbs-product-detail-price-row">
                        <span class="mbs-product-detail-price">{{ Mbs::price($product['price']) }}</span>
                        @if (!empty($product['old_price']))
                            <span class="mbs-product-detail-price-old">{{ Mbs::price((int) $product['old_price']) }}</span>
                        @endif
                    </div>

                    <p class="mbs-product-detail-stock {{ !empty($product['in_stock']) ? 'is-in-stock' : 'is-out-stock' }}">
                        {{ !empty($product['in_stock']) ? 'In Stock — Ready to ship' : 'Out of Stock' }}
                    </p>

                    <p class="mbs-product-detail-short">{{ $product['short_description'] }}</p>

                    <div class="mbs-product-purchase">
                        <div class="mbs-product-utility">
                            <button
                                type="button"
                                class="mbs-product-utility-link"
                                :class="{ 'is-active': isWishlisted(@js($product['slug'])) }"
                                @click.prevent="toggleWishlist(@js($product['slug']))"
                            >
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M12 21s-7-4.5-7-10a4 4 0 0 1 7-2 4 4 0 0 1 7 2c0 5.5-7 10-7 10Z"/></svg>
                                <span x-text="isWishlisted(@js($product['slug'])) ? 'Added to wishlist' : 'Add to wishlist'"></span>
                            </button>
                            <button
                                type="button"
                                class="mbs-product-utility-link"
                                :class="{ 'is-active': isCompared(@js($product['slug'])) }"
                                @click.prevent="toggleCompare(@js($product['slug']))"
                            >
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M4 4v5h5M20 20v-5h-5M4 15a8 8 0 0 0 13.66 2.66M20 9A8 8 0 0 0 6.34 6.34"/></svg>
                                <span x-text="isCompared(@js($product['slug'])) ? 'Added to compare' : 'Add to compare'"></span>
                            </button>
                            <button type="button" class="mbs-product-utility-link" @click.prevent="shareProduct()">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 1 1 0-2.684m0 2.684 6.632 3.316m-6.632-6 6.632-3.316m0 0a3 3 0 1 0 5.367-2.684 3 3 0 0 0-5.367 2.684Zm0 9.316a3 3 0 1 0 5.367 2.684 3 3 0 0 0-5.367-2.684Z"/></svg>
                                <span>Share</span>
                            </button>
                        </div>

                        <div class="mbs-product-purchase-row">
                            <div class="mbs-product-detail-qty">
                                <button type="button" @click="decreaseQty()" aria-label="Decrease quantity">−</button>
                                <span x-text="qty">1</span>
                                <button type="button" @click="increaseQty()" aria-label="Increase quantity">+</button>
                            </div>

                            <form action="{{ route('cart.add') }}" method="POST" class="mbs-product-detail-form mbs-product-detail-form--cart">
                                @csrf
                                <input type="hidden" name="slug" value="{{ $product['slug'] }}">
                                <input type="hidden" name="quantity" :value="qty">
                                <input type="hidden" name="redirect" value="cart">
                                <button type="submit" class="mbs-btn mbs-btn-primary mbs-product-detail-btn mbs-product-detail-btn--cart">Add to Cart</button>
                            </form>

                            <form action="{{ route('cart.add') }}" method="POST" class="mbs-product-detail-form mbs-product-detail-form--buy">
                                @csrf
                                <input type="hidden" name="slug" value="{{ $product['slug'] }}">
                                <input type="hidden" name="quantity" :value="qty">
                                <input type="hidden" name="redirect" value="checkout">
                                <button type="submit" class="mbs-btn mbs-btn-navy mbs-product-detail-btn mbs-product-detail-btn--buy">Buy It Now</button>
                            </form>
                        </div>

                        <ul class="mbs-product-delivery">
                            <li>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 18H9"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="7" cy="18" r="2" stroke-width="1.75"/><circle cx="17" cy="18" r="2" stroke-width="1.75"/></svg>
                                <span><strong>Estimated delivery:</strong> 5–7 days from order date across Pakistan.</span>
                            </li>
                            <li>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 0 0 3-3V8a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3v8a3 3 0 0 0 3 3Z"/></svg>
                                <span><strong>Free shipping & returns:</strong> On orders above Rs 15,000 in selected cities.</span>
                            </li>
                        </ul>

                        <x-payment-methods-strip variant="product" class="mbs-product-payments" />

                        <div class="mbs-product-contact">
                            <h3 class="mbs-product-contact-title">Let us know about your query!</h3>
                            <a href="{{ route('contact') }}" class="mbs-product-contact-link">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mbs-product-detail-tabs">
                <div class="mbs-product-detail-tab-nav">
                    <button type="button" :class="{ 'is-active': activeTab === 'description' }" @click="activeTab = 'description'">Description</button>
                    <button type="button" :class="{ 'is-active': activeTab === 'specifications' }" @click="activeTab = 'specifications'">Specifications</button>
                    <button type="button" :class="{ 'is-active': activeTab === 'reviews' }" @click="activeTab = 'reviews'">Reviews ({{ $product['review_count'] }})</button>
                </div>
                <div class="mbs-product-detail-tab-panel" x-show="activeTab === 'description'">
                    @php $productDescriptionHtml = Mbs::sanitizeProductHtml($product['description'] ?? ''); @endphp
                    @if ($productDescriptionHtml !== '')
                        <div class="mbs-product-description">
                            {!! $productDescriptionHtml !!}
                        </div>
                    @else
                        <p class="mbs-product-description-empty">Detailed description will be available soon. Contact us for specifications and pricing.</p>
                    @endif
                </div>
                <div class="mbs-product-detail-tab-panel" x-show="activeTab === 'specifications'" x-cloak>
                    <ul class="mbs-product-detail-specs">
                        @foreach ($product['specifications'] as $label => $value)
                            <li><span>{{ $label }}</span><strong>{{ $value }}</strong></li>
                        @endforeach
                    </ul>
                </div>
                <div class="mbs-product-detail-tab-panel" x-show="activeTab === 'reviews'" x-cloak>
                    @include('components.product-reviews-section')
                </div>
            </div>
        </div>
    </div>
</section>

@if (count($upsellProducts))
@include('components.product-relations-section', [
    'title' => 'Upgrade Your Setup',
    'subtitle' => 'Premium picks to enhance your experience',
    'products' => $upsellProducts,
    'gridClass' => 'mbs-product-grid--4',
])
@endif

@if (count($relatedProducts))
@include('components.product-relations-section', [
    'title' => 'You May Also Like',
    'subtitle' => 'Related products from our catalog',
    'products' => $relatedProducts,
    'gridClass' => 'mbs-product-grid--4',
])
@endif
@endsection
