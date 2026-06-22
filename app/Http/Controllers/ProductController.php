<?php

namespace App\Http\Controllers;

use App\Services\ProductRelationService;
use App\Services\ProductReviewService;
use App\Support\Mbs;
use App\Support\StorefrontData;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(string $slug, ProductReviewService $reviews, ProductRelationService $relations): View
    {
        $product = StorefrontData::findBySlug($slug);

        abort_unless($product, 404);

        $reviewStats = $reviews->stats($slug);
        $productReviews = $reviews->forProduct($slug);

        if ($reviewStats['count'] > 0) {
            $product['rating'] = $reviewStats['average'];
            $product['review_count'] = $reviewStats['count'];
        }

        $galleryUrls = array_map(fn ($img) => Mbs::image($img), $product['gallery']);
        $upsellProducts = $relations->forProduct($slug, 'upsell', 4);
        $relatedProducts = $relations->forProduct($slug, 'related', 4);

        return view('pages.product', [
            'product' => $product,
            'galleryUrls' => $galleryUrls,
            'upsellProducts' => $upsellProducts,
            'relatedProducts' => $relatedProducts,
            'productReviews' => $productReviews,
            'reviewStats' => $reviewStats,
        ]);
    }
}
