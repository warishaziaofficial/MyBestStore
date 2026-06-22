@php
    $rating = max(0, min(5, (float) ($rating ?? 0)));
    $fullStars = (int) floor($rating);
    $hasHalfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = max(0, 5 - $fullStars - ($hasHalfStar ? 1 : 0));
@endphp

<span class="product-stars" aria-label="Rating {{ number_format($rating, 1) }} out of 5">
    @for ($i = 0; $i < $fullStars; $i++)
        <span class="product-star product-star--full" aria-hidden="true">★</span>
    @endfor
    @if ($hasHalfStar)
        <span class="product-star product-star--half" aria-hidden="true"><span class="product-star-half-fill">★</span><span class="product-star-half-empty">★</span></span>
    @endif
    @for ($i = 0; $i < $emptyStars; $i++)
        <span class="product-star product-star--empty" aria-hidden="true">★</span>
    @endfor
</span>
