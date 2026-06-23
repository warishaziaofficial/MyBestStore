<?php

namespace App\Services;

use App\Models\ProductReview;
use App\Support\CmsIntegration;
use App\Support\StorefrontData;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class ProductReviewService
{
    /**
     * @return Collection<int, object>
     */
    public function forProduct(string $slug): Collection
    {
        if ($this->usesLegacyReviews()) {
            return ProductReview::query()
                ->where('product_slug', $slug)
                ->latest()
                ->get();
        }

        return $this->cmsReviewsForProduct($slug);
    }

    /**
     * @return array{average: float, count: int}
     */
    public function stats(string $slug): array
    {
        if ($this->usesLegacyReviews()) {
            $count = ProductReview::query()->where('product_slug', $slug)->count();

            if ($count === 0) {
                return ['average' => 0.0, 'count' => 0];
            }

            $average = (float) ProductReview::query()
                ->where('product_slug', $slug)
                ->avg('rating');

            return [
                'average' => round($average, 1),
                'count' => $count,
            ];
        }

        $product = StorefrontData::findBySlug($slug);

        if (! $product) {
            return ['average' => 0.0, 'count' => 0];
        }

        return [
            'average' => round((float) ($product['rating'] ?? 0), 1),
            'count' => (int) ($product['review_count'] ?? 0),
        ];
    }

    public function add(string $slug, string $name, int $rating, string $comment): ?ProductReview
    {
        if (! StorefrontData::findBySlug($slug)) {
            return null;
        }

        if (! $this->usesLegacyReviews()) {
            return null;
        }

        return ProductReview::query()->create([
            'product_slug' => $slug,
            'customer_name' => $name,
            'rating' => $rating,
            'comment' => $comment,
        ]);
    }

    private function usesLegacyReviews(): bool
    {
        return Schema::hasTable('product_reviews');
    }

    /**
     * @return Collection<int, object>
     */
    private function cmsReviewsForProduct(string $slug): Collection
    {
        $product = StorefrontData::findBySlug($slug);

        if (! $product || empty($product['id'])) {
            return collect();
        }

        $productId = (int) $product['id'];
        $reviews = collect();

        if (Schema::hasTable('Reviews')) {
            foreach (StorefrontData::productReviews($productId) as $review) {
                $reviews->push((object) [
                    'customer_name' => $review['reviewer_name'] ?? 'Customer',
                    'rating' => (int) ($product['rating'] ?? 5),
                    'comment' => $review['text'] ?? ($review['title'] ?? ''),
                    'created_at' => isset($review['created_at']) ? Carbon::parse($review['created_at']) : null,
                ]);
            }
        }

        if ($reviews->isEmpty() && Schema::hasTable('Ratings')) {
            foreach (StorefrontData::productRatings($productId) as $rating) {
                $reviews->push((object) [
                    'customer_name' => $rating['reviewer_name'] ?? 'Customer',
                    'rating' => (int) ($rating['rating'] ?? 5),
                    'comment' => 'Rated this product '.$rating['rating'].' out of 5.',
                    'created_at' => isset($rating['created_at']) ? Carbon::parse($rating['created_at']) : null,
                ]);
            }
        }

        return $reviews;
    }
}
