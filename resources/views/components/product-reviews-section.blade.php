@php
    $productReviews = $productReviews ?? collect();
    $reviewStats = $reviewStats ?? ['average' => 0, 'count' => 0];
@endphp

<div
    class="mbs-product-reviews"
    x-data="{
        reviewRating: {{ (float) $product['rating'] }},
        reviewCount: {{ (int) $product['review_count'] }},
        reviews: @js($productReviews->map(fn ($review) => [
            'customer_name' => $review->customer_name,
            'rating' => $review->rating,
            'comment' => $review->comment,
            'created_at' => $review->created_at?->format('M j, Y'),
        ])->values()->all()),
        reviewSubmitting: false,
        reviewMessage: '',
        reviewError: '',
        async submitReview(event) {
            event.preventDefault();
            this.reviewSubmitting = true;
            this.reviewMessage = '';
            this.reviewError = '';

            const form = event.target;
            const formData = new FormData(form);

            if (!formData.get('rating') || Number(formData.get('rating')) < 1) {
                this.reviewError = 'Please select a star rating.';
                this.reviewSubmitting = false;
                return;
            }

            try {
                const response = await fetch(@js(route('product.reviews.store', $product['slug'])), {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Review could not be submitted.');
                }

                this.reviews.unshift(data.review);
                this.reviewRating = data.stats.average;
                this.reviewCount = data.stats.count;
                this.reviewMessage = data.message;
                form.reset();
                window.showToast(data.message, 'success');
            } catch (error) {
                this.reviewError = error.message || 'Review could not be submitted.';
            } finally {
                this.reviewSubmitting = false;
            }
        }
    }"
>
    <div class="mbs-product-reviews-summary">
        <div class="mbs-product-reviews-score">
            <strong class="mbs-product-reviews-average" x-text="reviewRating.toFixed(1)">0.0</strong>
            <div class="mbs-product-reviews-stars">
                @include('components.product-stars', ['rating' => $product['rating']])
            </div>
            <p class="mbs-product-reviews-count"><span x-text="reviewCount">{{ $product['review_count'] }}</span> customer reviews</p>
        </div>
        <p class="mbs-product-reviews-note">Share your experience to help other shoppers choose the right product.</p>
    </div>

    <div class="mbs-product-reviews-layout">
        <div class="mbs-product-review-form-wrap">
            <h3 class="mbs-product-reviews-heading">Write a Review</h3>
            <form class="mbs-product-review-form" @submit="submitReview">
                @csrf
                <div class="mbs-product-review-field">
                    <label for="review-customer-name">Your Name</label>
                    <input id="review-customer-name" type="text" name="customer_name" class="mbs-input" placeholder="Enter your name" required maxlength="120">
                </div>

                <div class="mbs-product-review-field">
                    <label>Your Rating</label>
                    <div :key="`review-stars-${reviews.length}`">
                        @include('components.product-star-input', ['rating' => 0])
                    </div>
                </div>

                <div class="mbs-product-review-field">
                    <label for="review-comment">Your Review</label>
                    <textarea id="review-comment" name="comment" class="mbs-input mbs-product-review-textarea" rows="5" placeholder="Tell us about the product quality, delivery and your overall experience..." required minlength="10" maxlength="2000"></textarea>
                </div>

                <p class="mbs-product-review-error" x-show="reviewError" x-text="reviewError" x-cloak></p>
                <p class="mbs-product-review-success" x-show="reviewMessage" x-text="reviewMessage" x-cloak></p>

                <button type="submit" class="mbs-btn mbs-btn-primary mbs-product-review-submit" :disabled="reviewSubmitting">
                    <span x-show="!reviewSubmitting">Submit Review</span>
                    <span x-show="reviewSubmitting" x-cloak>Submitting...</span>
                </button>
            </form>
        </div>

        <div class="mbs-product-review-list-wrap">
            <h3 class="mbs-product-reviews-heading">Customer Reviews</h3>

            <div class="mbs-product-review-empty" x-show="reviews.length === 0" x-cloak>
                <p>No reviews yet. Be the first to review this product.</p>
            </div>

            <div class="mbs-product-review-list">
                <template x-for="(review, index) in reviews" :key="`${review.customer_name}-${index}`">
                    <article class="mbs-product-review-item">
                        <div class="mbs-product-review-item-head">
                            <div>
                                <strong class="mbs-product-review-author" x-text="review.customer_name"></strong>
                                <p class="mbs-product-review-date" x-text="review.created_at"></p>
                            </div>
                            <div class="mbs-product-review-item-stars" aria-hidden="true">
                                <template x-for="star in 5" :key="star">
                                    <span class="product-star" :class="star <= review.rating ? 'product-star--full' : 'product-star--empty'">★</span>
                                </template>
                            </div>
                        </div>
                        <p class="mbs-product-review-text" x-text="review.comment"></p>
                    </article>
                </template>
            </div>
        </div>
    </div>
</div>
