@php
    use App\Support\Mbs;
    $product = $product ?? [];
    $name = $product['name'] ?? 'Product';
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
@endphp

<article class="product-card mbs-product-card group">
    <div class="product-image-wrap">
        <div class="product-image-inner">
            <a href="{{ route('shop') }}" class="product-image-link">
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
            <button type="button" @click="quickViewOpen = true; quickViewTitle = @js($name); quickViewImage = @js($image)" class="product-action-btn" aria-label="Quick view">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3" stroke-width="2"/></svg>
            </button>
            <button type="button" class="product-action-btn" aria-label="Wishlist">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M12 21s-7-4.5-7-10a4 4 0 0 1 7-2 4 4 0 0 1 7 2c0 5.5-7 10-7 10Z"/></svg>
            </button>
            <button type="button" @click="cartOpen = true" class="product-action-btn cart-active" aria-label="Add to cart">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M6 7h15l-1.5 9h-12z"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
            </button>
            <button type="button" class="product-action-btn" aria-label="Compare">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 4v5h5M20 20v-5h-5M4 15a8 8 0 0 0 13.66 2.66M20 9A8 8 0 0 0 6.34 6.34"/></svg>
            </button>
        </div>
    </div>
    <div class="product-card-body">
        <h3 class="product-title line-clamp-2">
            <a href="{{ route('shop') }}">{{ $name }}</a>
        </h3>
        <div class="product-rating">
            <span class="product-stars star-rating">{{ Mbs::stars($rating) }}</span>
            @if ($reviewCount)
                <span class="product-review-count">({{ $reviewCount }})</span>
            @endif
        </div>
        <div class="product-price-row">
            <span class="product-price">{{ Mbs::price($price) }}</span>
            @if ($oldPrice)
                <span class="product-price-old">{{ Mbs::price((int) $oldPrice) }}</span>
            @endif
        </div>
        <button type="button" @click="cartOpen = true" class="product-cart-btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M6 7h15l-1.5 9h-12z"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
            Add to Cart
        </button>
    </div>
</article>
