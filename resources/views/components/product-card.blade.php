@php

    use App\Support\Mbs;

    $product = $product ?? [];

    $name = $product['name'] ?? 'Product';

    $slug = $product['slug'] ?? '';

    $price = (int) ($product['price'] ?? 0);

    $oldPrice = $product['old_price'] ?? null;

    $image = Mbs::image($product['image'] ?? 'placeholder-product.svg');

    $rating = (float) ($product['rating'] ?? 4.5);

    $reviewCount = (int) ($product['review_count'] ?? 0);

    $badge = $product['badge'] ?? null;

    $showSale = $badge === 'SALE' || ($oldPrice && $oldPrice > $price);

    $showNew = $badge === 'NEW' || (!empty($product['featured']) && !$showSale);

    $showFeatured = $badge === 'FEATURED' && !$showSale && !$showNew;

    $badgeLabel = $showSale ? 'SALE' : ($showNew ? 'NEW' : ($showFeatured ? 'FEATURED' : null));

    $productUrl = $slug ? route('product.show', $slug) : route('shop');

@endphp



<article class="product-card mbs-product-card group">

    <div class="product-image-wrap">

        <div class="product-image-inner">

            <a href="{{ $productUrl }}" class="product-image-link" @click="searchOpen = false">

                <img

                    src="{{ $image }}"

                    alt="{{ $product['image_alt'] ?? $name }}"

                    class="product-image"

                    loading="lazy"

                >

            </a>

        </div>

        @if ($badgeLabel)

            <span @class([

                'product-badge',

                'product-badge--sale' => $showSale,

                'product-badge--new' => $showNew,

                'product-badge--featured' => $showFeatured,

            ])>{{ $badgeLabel }}</span>

        @endif

        <div class="product-actions">

            <div class="product-action-item">
                <span class="product-action-label">Quick view</span>
                <a href="{{ $productUrl }}" class="product-action-btn" aria-label="View product" @click="searchOpen = false">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3" stroke-width="2"/></svg>
                </a>
            </div>

            @if ($slug)
                <div class="product-action-item">
                    <span
                        class="product-action-label"
                        :class="{ 'product-action-label--active': isWishlisted(@js($slug)) }"
                        x-text="isWishlisted(@js($slug)) ? 'Added to wishlist' : 'Add to wishlist'"
                    ></span>
                    <button
                        type="button"
                        class="product-action-btn product-action-btn--wishlist"
                        :class="{ 'is-active': isWishlisted(@js($slug)) }"
                        @click.prevent="toggleWishlist(@js($slug))"
                        aria-label="Wishlist"
                    >
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M12 21s-7-4.5-7-10a4 4 0 0 1 7-2 4 4 0 0 1 7 2c0 5.5-7 10-7 10Z"/></svg>
                    </button>
                </div>

                <div class="product-action-item">
                    <span class="product-action-label">Add to cart</span>
                    <button type="button" class="product-action-btn" aria-label="Add to cart" @click.prevent="addToCart(@js($slug))">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M6 7h15l-1.5 9h-12z"/><path stroke-linecap="round" stroke-width="2" d="M6 7l-2-3H1"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
                    </button>
                </div>

                <div class="product-action-item">
                    <span
                        class="product-action-label"
                        :class="{ 'product-action-label--active': isCompared(@js($slug)) }"
                        x-text="isCompared(@js($slug)) ? 'Added to compare' : 'Add to compare'"
                    ></span>
                    <button
                        type="button"
                        class="product-action-btn product-action-btn--compare"
                        :class="{ 'is-active': isCompared(@js($slug)) }"
                        @click.prevent="toggleCompare(@js($slug))"
                        aria-label="Compare"
                    >
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 4v5h5M20 20v-5h-5M4 15a8 8 0 0 0 13.66 2.66M20 9A8 8 0 0 0 6.34 6.34"/></svg>
                    </button>
                </div>
            @else
                <div class="product-action-item">
                    <span class="product-action-label">Add to wishlist</span>
                    <a href="{{ route('wishlist') }}" class="product-action-btn" aria-label="Wishlist">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M12 21s-7-4.5-7-10a4 4 0 0 1 7-2 4 4 0 0 1 7 2c0 5.5-7 10-7 10Z"/></svg>
                    </a>
                </div>

                <div class="product-action-item">
                    <span class="product-action-label">Add to cart</span>
                    <button type="button" @click.prevent="cartOpen = true" class="product-action-btn" aria-label="Add to cart">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M6 7h15l-1.5 9h-12z"/><path stroke-linecap="round" stroke-width="2" d="M6 7l-2-3H1"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
                    </button>
                </div>

                <div class="product-action-item">
                    <span class="product-action-label">Add to compare</span>
                    <a href="{{ route('compare') }}" class="product-action-btn" aria-label="Compare">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 4v5h5M20 20v-5h-5M4 15a8 8 0 0 0 13.66 2.66M20 9A8 8 0 0 0 6.34 6.34"/></svg>
                    </a>
                </div>
            @endif

        </div>

    </div>

    <div class="product-card-body mbs-product-card-body">

        <h3 class="product-title line-clamp-2">

            <a href="{{ $productUrl }}" @click="searchOpen = false">{{ $name }}</a>

        </h3>

        <div class="product-rating">

            @include('components.product-stars', ['rating' => $rating])

            @if ($reviewCount)

                <span class="product-review-count">({{ $reviewCount }})</span>

            @endif

        </div>

        <div class="product-price-row">

            <span class="product-price">{{ Mbs::price($price) }}</span>

            @if ($oldPrice)

                <span class="product-price-old product-old-price">{{ Mbs::price((int) $oldPrice) }}</span>

            @endif

        </div>

        @if ($slug)
            <button type="button" class="product-cart-btn" @click.prevent="addToCart(@js($slug))">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M6 7h15l-1.5 9h-12z"/><path stroke-linecap="round" stroke-width="2" d="M6 7l-2-3H1"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
                Add to Cart
            </button>
        @else
            <button type="button" @click="cartOpen = true" class="product-cart-btn">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M6 7h15l-1.5 9h-12z"/><path stroke-linecap="round" stroke-width="2" d="M6 7l-2-3H1"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
                Add to Cart
            </button>
        @endif

    </div>

</article>

