<?php

namespace Scripts\Lib;

use App\Models\Category as LaravelCategory;
use App\Models\Product as LaravelProduct;
use App\Models\ProductImage as LaravelProductImage;
use Cms\Models\Category as CmsCategory;
use Cms\Models\Product as CmsProduct;
use Cms\Models\ProductImage as CmsProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

interface CatalogWriter
{
  public function mode(): string;

  public function findCategory(string $slug, string $name): ?object;

  /** @param array<string, mixed> $payload */
  public function upsertCategory(array $payload, ?object $existing): object;

  public function findProduct(string $slug, string $name, string $sku): ?object;

  /** @param array<string, mixed> $payload */
  public function upsertProduct(array $payload, ?object $existing): object;

  /** @param list<array{path: string, alt: string, sort_order: int, is_primary: bool}> $images */
  public function syncProductImages(object $product, array $images): void;

  public function refreshCategoryCounts(): void;
}

class LaravelCatalogWriter implements CatalogWriter
{
  public function mode(): string
  {
    return 'laravel';
  }

  public function findCategory(string $slug, string $name): ?object
  {
    return LaravelCategory::query()
      ->where('slug', $slug)
      ->orWhereRaw('LOWER(name) = ?', [strtolower($name)])
      ->first();
  }

  public function upsertCategory(array $payload, ?object $existing): object
  {
    $data = [
      'name' => $payload['name'],
      'slug' => $payload['slug'],
      'description' => $payload['description'],
      'image' => $payload['image'],
      'is_active' => true,
    ];

    if ($existing) {
      $existing->update($data);

      return $existing->fresh();
    }

    return LaravelCategory::query()->create($data);
  }

  public function findProduct(string $slug, string $name, string $sku): ?object
  {
    $bySlug = LaravelProduct::query()->where('slug', $slug)->first();

    if ($bySlug) {
      return $bySlug;
    }

    if ($sku !== '') {
      $bySku = LaravelProduct::query()->where('sku', $sku)->first();

      if ($bySku) {
        return $bySku;
      }
    }

    return LaravelProduct::query()
      ->whereRaw('LOWER(name) = ?', [strtolower($name)])
      ->first();
  }

  public function upsertProduct(array $payload, ?object $existing): object
  {
    $data = [
      'category_id' => $payload['category_id'],
      'name' => $payload['name'],
      'slug' => $payload['slug'],
      'sku' => $payload['sku'] ?: null,
      'description' => $payload['description'],
      'price' => $payload['price'],
      'compare_at_price' => null,
      'weight' => 0,
      'status' => 'active',
    ];

    if ($existing) {
      $existing->update($data);

      return $existing->fresh();
    }

    return LaravelProduct::query()->create($data);
  }

  public function syncProductImages(object $product, array $images): void
  {
    foreach ($images as $image) {
      $exists = LaravelProductImage::query()
        ->where('product_id', $product->id)
        ->where('path', $image['path'])
        ->exists();

      if ($exists) {
        continue;
      }

      LaravelProductImage::query()->create([
        'product_id' => $product->id,
        'path' => $image['path'],
        'alt' => $image['alt'],
        'sort_order' => $image['sort_order'],
        'is_primary' => $image['is_primary'],
      ]);
    }
  }

  public function refreshCategoryCounts(): void
  {
    // Laravel categories table has no count column.
  }
}

class CmsCatalogWriter implements CatalogWriter
{
  public function mode(): string
  {
    return 'cms';
  }

  public function findCategory(string $slug, string $name): ?object
  {
    return CmsCategory::query()
      ->where('slug', $slug)
      ->orWhereRaw('LOWER(name) = ?', [strtolower($name)])
      ->first();
  }

  public function upsertCategory(array $payload, ?object $existing): object
  {
    $data = [
      'name' => $payload['name'],
      'slug' => $payload['slug'],
      'image' => $payload['image'],
      'image_alt' => $payload['name'],
      'description' => $payload['description'],
      'count' => (int) ($existing->count ?? 0),
    ];

    if ($existing) {
      $existing->update($data);

      return $existing->fresh();
    }

    return CmsCategory::query()->create($data);
  }

  public function findProduct(string $slug, string $name, string $sku): ?object
  {
    $bySlug = CmsProduct::query()->where('slug', $slug)->first();

    if ($bySlug) {
      return $bySlug;
    }

    if ($sku !== '') {
      $bySku = CmsProduct::query()
        ->where('description', 'like', '%SKU:</strong> '.$sku.'%')
        ->first();

      if ($bySku) {
        return $bySku;
      }
    }

    return CmsProduct::query()
      ->whereRaw('LOWER(name) = ?', [strtolower($name)])
      ->first();
  }

  public function upsertProduct(array $payload, ?object $existing): object
  {
    $data = [
      'name' => $payload['name'],
      'slug' => $payload['slug'],
      'price' => (int) $payload['price'],
      'old_price' => null,
      'image' => $payload['image'],
      'image_alt' => $payload['name'],
      'category' => $payload['category_slug'],
      'sub_category' => $payload['sub_category'],
      'description' => $payload['description'],
      'rating' => $existing?->rating ?? 4.5,
      'review_count' => $existing?->review_count ?? 0,
      'badge' => $existing?->badge ?? null,
      'featured' => (bool) ($existing?->featured ?? false),
      'brand' => $payload['brand'],
      'stock' => $payload['stock'],
    ];

    if ($existing) {
      $existing->update($data);

      return $existing->fresh();
    }

    return CmsProduct::query()->create($data);
  }

  public function syncProductImages(object $product, array $images): void
  {
    foreach ($images as $image) {
      $exists = CmsProductImage::query()
        ->where('product_id', $product->id)
        ->where('image', $image['path'])
        ->exists();

      if ($exists) {
        continue;
      }

      CmsProductImage::query()->create([
        'product_id' => $product->id,
        'image' => $image['path'],
        'alt_text' => $image['alt'],
        'sort_order' => $image['sort_order'],
      ]);
    }
  }

  public function refreshCategoryCounts(): void
  {
    $counts = CmsProduct::query()
      ->select('category', DB::raw('COUNT(*) as total'))
      ->groupBy('category')
      ->pluck('total', 'category');

    foreach ($counts as $slug => $count) {
      CmsCategory::query()->where('slug', $slug)->update(['count' => (int) $count]);
    }
  }
}

class CatalogWriterFactory
{
  public static function make(): CatalogWriter
  {
    $driver = DB::connection()->getDriverName();

    if ($driver === 'sqlite') {
      return new LaravelCatalogWriter;
    }

    CmsSchemaBootstrap::ensureCatalogTables();

    return new CmsCatalogWriter;
  }
}

class CatalogJsonExporter
{
  public static function exportFromLaravel(): void
  {
    $categories = LaravelCategory::query()->orderBy('name')->get()->map(fn (LaravelCategory $cat) => [
      'id' => $cat->slug,
      'name' => $cat->name,
      'slug' => $cat->slug,
      'productCount' => LaravelProduct::query()->where('category_id', $cat->id)->count(),
      'image' => $cat->image ?: 'placeholder-product.svg',
      'imageAlt' => $cat->name,
      'description' => $cat->description ?: 'Browse products in this category.',
    ])->values()->all();

    $products = LaravelProduct::query()
      ->with(['category', 'images'])
      ->orderByDesc('id')
      ->get()
      ->map(function (LaravelProduct $product) {
        $primary = $product->images->sortBy('sort_order')->first();

        return [
          'id' => $product->slug,
          'name' => $product->name,
          'slug' => $product->slug,
          'price' => (int) $product->price,
          'image' => $primary?->path ?? 'placeholder-product.svg',
          'imageAlt' => $product->name,
          'category' => $product->category?->slug ?? '',
          'subCategory' => $product->category?->name ?? '',
          'description' => $product->description,
          'rating' => 4.5,
          'reviewCount' => 0,
          'featured' => false,
          'brand' => '',
          'gallery' => $product->images->sortBy('sort_order')->pluck('path')->values()->all(),
        ];
      })->values()->all();

    $payload = [
      'allProducts' => $products,
      'categories' => $categories,
      'exports' => [
        'categories' => $categories,
      ],
    ];

    $path = base_path('scripts/generated-catalog.json');
    file_put_contents($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
  }

  public static function ensureStorefrontFallback(): void
  {
    $envPath = base_path('.env');

    if (! is_file($envPath)) {
      return;
    }

    $env = file_get_contents($envPath);

    if ($env === false) {
      return;
    }

    if (preg_match('/^STOREFRONT_DEV_FALLBACK=/m', $env)) {
      $env = preg_replace('/^STOREFRONT_DEV_FALLBACK=.*/m', 'STOREFRONT_DEV_FALLBACK=true', $env);
    } else {
      $env .= "\nSTOREFRONT_DEV_FALLBACK=true\n";
    }

    file_put_contents($envPath, $env);
  }
}
