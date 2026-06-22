@php
    use App\Support\Mbs;
    use App\Support\StorefrontData;

    $carouselProducts = $carouselProducts ?? StorefrontData::carouselProducts(8);
@endphp

<section class="mbs-feature-strip" aria-label="Shop electronics">
    <div class="mbs-container">
        <div class="mbs-feature-carousel" data-feature-carousel>
            <button type="button" class="mbs-feature-carousel-nav mbs-feature-carousel-nav--prev" aria-label="Previous products">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>

            <div class="mbs-feature-carousel-viewport">
                <div class="mbs-feature-carousel-track">
                    @foreach ($carouselProducts as $product)
                        <a href="{{ route('product.show', $product['slug']) }}" class="mbs-feature-carousel-item">
                            <div class="mbs-feature-carousel-thumb">
                                <img
                                    src="{{ Mbs::image($product['image']) }}"
                                    alt="{{ $product['image_alt'] ?? $product['name'] }}"
                                    class="mbs-feature-carousel-image"
                                    loading="lazy"
                                >
                            </div>
                            <span class="mbs-feature-carousel-label">{{ $product['name'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <button type="button" class="mbs-feature-carousel-nav mbs-feature-carousel-nav--next" aria-label="Next products">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>
</section>
