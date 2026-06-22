<?php

namespace App\Http\Controllers;

use App\Services\ProductReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function __construct(private readonly ProductReviewService $reviews) {}

    public function store(Request $request, string $slug): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $review = $this->reviews->add(
            $slug,
            $validated['customer_name'],
            (int) $validated['rating'],
            $validated['comment']
        );

        if (! $review) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.',
                ], 404);
            }

            return back()->with('error', 'Product not found.');
        }

        $stats = $this->reviews->stats($slug);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your review has been submitted.',
                'stats' => $stats,
                'review' => [
                    'customer_name' => $review->customer_name,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at?->format('M j, Y'),
                ],
            ]);
        }

        return back()->with('success', 'Thank you! Your review has been submitted.');
    }
}
