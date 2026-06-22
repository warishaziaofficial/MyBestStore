<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\BarcodeService;
use App\Services\ProductRelationService;
use App\Support\StorefrontData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductCatalogController extends Controller
{
    public function __construct(
        private readonly ProductRelationService $relations,
        private readonly BarcodeService $barcodes,
    ) {}

    public function index(Request $request): View
    {
        $query = strtolower(trim($request->string('q')->toString()));
        $products = StorefrontData::allProducts();

        if ($query !== '') {
            $products = array_values(array_filter($products, function (array $product) use ($query) {
                $haystack = strtolower(implode(' ', [
                    $product['name'] ?? '',
                    $product['slug'] ?? '',
                    $product['category'] ?? '',
                    $product['brand'] ?? '',
                ]));

                return str_contains($haystack, $query);
            }));
        }

        return view('admin.products.index', [
            'products' => $products,
            'query' => $request->string('q')->toString(),
        ]);
    }

    public function edit(string $slug): View|RedirectResponse
    {
        $product = StorefrontData::findBySlug($slug);

        if (! $product) {
            return redirect()->route('admin.products.index')->with('error', 'Product not found.');
        }

        $dbProduct = Product::query()->where('slug', $slug)->first();
        $selected = $this->relations->selectedSlugsForProduct($slug);

        return view('admin.products.edit', [
            'product' => $product,
            'dbProduct' => $dbProduct,
            'allProducts' => StorefrontData::allProducts(),
            'selectedUpsell' => $selected['upsell'],
            'selectedCrossSell' => $selected['cross_sell'],
            'selectedRelated' => $selected['related'],
        ]);
    }

    public function update(Request $request, string $slug): RedirectResponse
    {
        $storeProduct = StorefrontData::findBySlug($slug);

        if (! $storeProduct) {
            return redirect()->route('admin.products.index')->with('error', 'Product not found.');
        }

        $validated = $request->validate([
            'sku' => ['nullable', 'string', 'max:120'],
            'barcode' => ['nullable', 'string', 'max:120'],
            'upsell' => ['nullable', 'array'],
            'upsell.*' => ['string', 'max:255'],
            'cross_sell' => ['nullable', 'array'],
            'cross_sell.*' => ['string', 'max:255'],
            'related' => ['nullable', 'array'],
            'related.*' => ['string', 'max:255'],
        ]);

        $product = Product::query()->firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $storeProduct['name'],
                'price' => $storeProduct['price'],
                'status' => 'active',
            ]
        );

        $product->update([
            'name' => $storeProduct['name'],
            'sku' => $validated['sku'] ?? null,
            'barcode' => $validated['barcode'] ?? null,
        ]);

        if (! filled($product->barcode)) {
            $product->update([
                'barcode' => $this->barcodes->makeProductBarcode($product->fresh()),
            ]);
        }

        $this->relations->syncForProduct($slug, [
            'upsell' => $validated['upsell'] ?? [],
            'cross_sell' => $validated['cross_sell'] ?? [],
            'related' => $validated['related'] ?? [],
        ]);

        return redirect()
            ->route('admin.products.edit', $slug)
            ->with('success', 'Product details updated successfully.');
    }
}
