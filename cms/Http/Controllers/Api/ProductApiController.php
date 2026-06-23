<?php

namespace Cms\Http\Controllers\Api;

use Cms\Http\Controllers\Controller;
use Cms\Models\Product;
use Cms\Support\AdminApiFormatter;
use Cms\Support\CmsAuth;
use Cms\Support\MediaStorage;
use Cms\Support\StockAlertNotifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProductApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (! Schema::hasTable('Products')) {
            return response()->json(['data' => [], 'meta' => ['total' => 0]]);
        }

        $query = Product::query()->orderByDesc('id');

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', '%'.$search.'%')
                    ->orWhere('category', 'like', '%'.$search.'%')
                    ->orWhere('brand', 'like', '%'.$search.'%')
                    ->orWhere('slug', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        if ($request->filled('featured')) {
            $query->where('featured', filter_var($request->query('featured'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('brand')) {
            $query->where('brand', $request->query('brand'));
        }

        $paginator = $query->paginate((int) $request->query('per_page', 20));

        return response()->json([
            'data' => collect($paginator->items())
                ->map(fn (Product $product) => AdminApiFormatter::product($product))
                ->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $product = Product::query()->findOrFail($id);

        return response()->json([
            'data' => AdminApiFormatter::product($product),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->requireEditor();

        $data = $this->validated($request);
        $product = Product::create($data);

        return response()->json([
            'message' => 'Product created.',
            'data' => AdminApiFormatter::product($product),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $this->requireEditor();

        $product = Product::query()->findOrFail($id);
        $previousStock = (int) $product->stock;
        $product->update($this->validated($request, $product));
        StockAlertNotifier::afterStockChange((int) $product->id, $previousStock);

        return response()->json([
            'message' => 'Product updated.',
            'data' => AdminApiFormatter::product($product->fresh()),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->requireEditor();

        Product::query()->findOrFail($id)->delete();

        return response()->json(['message' => 'Product deleted.']);
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'name' => [$product ? 'sometimes' : 'required', 'string', 'max:255'],
            'slug' => [$product ? 'sometimes' : 'required', 'string', 'max:255', 'unique:Products,slug'.($product ? ','.$product->id : '')],
            'price' => [$product ? 'sometimes' : 'required', 'integer', 'min:0'],
            'old_price' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'string', 'max:500'],
            'image_file' => ['nullable', 'file', 'image', 'max:5120'],
            'image_alt' => [$product ? 'sometimes' : 'required', 'string', 'max:255'],
            'category' => [$product ? 'sometimes' : 'required', 'string', 'max:100'],
            'sub_category' => [$product ? 'sometimes' : 'required', 'string', 'max:100'],
            'brand' => [$product ? 'sometimes' : 'required', 'string', 'max:100'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'badge' => ['nullable', 'string', 'max:20'],
            'featured' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('image_file')) {
            $stored = MediaStorage::store($request->file('image_file'));
            $data['image'] = $stored['path'];
        }

        unset($data['image_file']);

        if (! $product) {
            $data['rating'] = 0;
            $data['review_count'] = 0;
            $data['stock'] = (int) ($data['stock'] ?? 0);
            $data['featured'] = (bool) ($data['featured'] ?? false);
        }

        if (array_key_exists('featured', $data)) {
            $data['featured'] = (bool) $data['featured'];
        }

        if (array_key_exists('stock', $data)) {
            $data['stock'] = (int) $data['stock'];
        }

        if (empty($data['image'] ?? null) && $product) {
            $data['image'] = $product->image;
        }

        return $data;
    }

    private function requireEditor(): void
    {
        if (! CmsAuth::canEdit()) {
            abort(403, 'You do not have permission to perform this action.');
        }
    }
}
