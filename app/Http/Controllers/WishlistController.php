<?php

namespace App\Http\Controllers;

use App\Services\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct(private readonly WishlistService $wishlist) {}

    public function index()
    {
        return view('pages.wishlist', [
            'products' => $this->wishlist->products(),
        ]);
    }

    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string'],
        ]);

        $slugs = $this->wishlist->toggle($validated['slug']);

        return response()->json([
            'slugs' => $slugs,
            'count' => count($slugs),
            'active' => in_array($validated['slug'], $slugs, true),
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'slugs' => ['nullable', 'array'],
            'slugs.*' => ['string'],
        ]);

        $slugs = $this->wishlist->merge($validated['slugs'] ?? []);

        return response()->json([
            'slugs' => $slugs,
            'count' => count($slugs),
        ]);
    }

    public function remove(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string'],
        ]);

        $slugs = $this->wishlist->remove($validated['slug']);

        if ($request->wantsJson()) {
            return response()->json([
                'slugs' => $slugs,
                'count' => count($slugs),
            ]);
        }

        return back()->with('success', 'Product removed from wishlist.');
    }
}
