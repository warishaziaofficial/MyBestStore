<?php

namespace App\Services;

use App\Models\ProductReview;
use App\Support\StorefrontData;
use Illuminate\Support\Collection;

class ProductReviewService
{
    /**
     * @return Collection<int, ProductReview>
     */
    public function forProduct(string $slug): Collection
    {
        return ProductReview::query()
            ->where('product_slug', $slug)
            ->latest()
            ->get();
    }

    /**
     * @return array{average: float, count: int}
     */
    public function stats(string $slug): array
    {
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

    public function add(string $slug, string $name, int $rating, string $comment): ?ProductReview
    {
        if (! StorefrontData::findBySlug($slug)) {
            return null;
        }

        return ProductReview::query()->create([
            'product_slug' => $slug,
            'customer_name' => $name,
            'rating' => $rating,
            'comment' => $comment,
        ]);
    }
}
