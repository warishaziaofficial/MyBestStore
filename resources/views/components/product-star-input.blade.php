@php
    $rating = max(1, min(5, (int) ($rating ?? 0)));
@endphp

<div
    class="product-star-input"
    x-data="{ rating: {{ $rating }}, hoverRating: 0 }"
    @mouseleave="hoverRating = 0"
>
    <input type="hidden" name="rating" :value="rating" required>

    <div class="product-star-input-stars" role="radiogroup" aria-label="Select rating">
        @for ($star = 1; $star <= 5; $star++)
            <button
                type="button"
                class="product-star-input-btn"
                :class="{ 'is-active': (hoverRating ? hoverRating >= {{ $star }} : rating >= {{ $star }}) }"
                @mouseenter="hoverRating = {{ $star }}"
                @click="rating = {{ $star }}"
                aria-label="Rate {{ $star }} out of 5"
            >★</button>
        @endfor
    </div>

    <p class="product-star-input-label" x-text="rating ? `${rating} out of 5 stars` : 'Select your rating'"></p>
</div>
