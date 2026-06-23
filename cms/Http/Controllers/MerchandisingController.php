<?php

namespace Cms\Http\Controllers;

use Cms\Models\Product;
use Cms\Models\ProductPlacement;
use Cms\Support\CmsAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MerchandisingController extends Controller
{
    public function featured(): View
    {
        return $this->placementView('featured', 'Featured Products');
    }

    public function newArrivals(): View
    {
        return $this->placementView('new_arrival', 'New Arrivals');
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $this->requireEditor();

        $placement = $type === 'new-arrivals' ? 'new_arrival' : 'featured';

        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:Products,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        ProductPlacement::query()->updateOrCreate(
            [
                'product_id' => $data['product_id'],
                'placement' => $placement,
            ],
            [
                'sort_order' => (int) ($data['sort_order'] ?? 0),
                'is_active' => true,
            ]
        );

        $route = $placement === 'featured' ? 'cms.merchandising.featured' : 'cms.merchandising.new-arrivals';

        return redirect()->route($route)->with('success', 'Product added to list.');
    }

    public function destroy(string $type, int $id): RedirectResponse
    {
        $this->requireEditor();

        ProductPlacement::query()->findOrFail($id)->delete();

        $route = $type === 'new-arrivals' ? 'cms.merchandising.new-arrivals' : 'cms.merchandising.featured';

        return redirect()->route($route)->with('success', 'Product removed from list.');
    }

    private function placementView(string $placement, string $title): View
    {
        $items = ProductPlacement::query()
            ->with('product')
            ->where('placement', $placement)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $availableProducts = Product::query()->orderBy('name')->get(['id', 'name', 'slug']);

        return view('cms::merchandising.index', [
            'title' => $title,
            'placement' => $placement,
            'type' => $placement === 'new_arrival' ? 'new-arrivals' : 'featured',
            'items' => $items,
            'availableProducts' => $availableProducts,
            'canEdit' => CmsAuth::canEdit(),
        ]);
    }

    private function requireEditor(): void
    {
        if (! CmsAuth::canEdit()) {
            abort(403);
        }
    }
}
