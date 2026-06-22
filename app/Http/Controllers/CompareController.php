<?php

namespace App\Http\Controllers;

use App\Services\CompareService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function __construct(private readonly CompareService $compare) {}

    public function index()
    {
        return view('pages.compare', [
            'products' => $this->compare->products(),
            'maxItems' => (int) config('compare.max_items', 4),
        ]);
    }

    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string'],
        ]);

        $result = $this->compare->toggle($validated['slug']);
        $slugs = $result['slugs'];
        $active = in_array($validated['slug'], $slugs, true);

        $message = match (true) {
            $result['limited'] => 'You can compare up to '.config('compare.max_items', 4).' products at a time.',
            $active => 'Product added to compare.',
            default => 'Product removed from compare.',
        };

        return response()->json([
            'slugs' => $slugs,
            'count' => count($slugs),
            'active' => $active,
            'limited' => $result['limited'],
            'message' => $message,
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'slugs' => ['nullable', 'array'],
            'slugs.*' => ['string'],
        ]);

        $slugs = $this->compare->merge($validated['slugs'] ?? []);

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

        $slugs = $this->compare->remove($validated['slug']);

        if ($request->wantsJson()) {
            return response()->json([
                'slugs' => $slugs,
                'count' => count($slugs),
            ]);
        }

        return back()->with('success', 'Product removed from compare.');
    }
}
