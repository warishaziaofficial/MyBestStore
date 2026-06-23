<?php

namespace App\Services;

use App\Models\Product;
use App\Support\CmsIntegration;
use App\Support\Mbs;
use App\Support\StorefrontData;
use Illuminate\Http\Request;
class ProductCatalogService
{
    public const SORT_OPTIONS = [
        'featured' => 'Featured',
        'newest' => 'Newest',
        'price_asc' => 'Price: Low to High',
        'price_desc' => 'Price: High to Low',
        'name_asc' => 'Name A to Z',
        'rating_desc' => 'Best Rated',
    ];

    /**
     * @return array<string, mixed>
     */
    public function browse(Request $request, string $context = 'shop'): array
    {
        $options = $this->contextOptions($context, $request);
        $baseProducts = $this->baseProducts($context, $options);
        $filters = $this->resolveFilters($request, $options);
        $filtered = $this->applyFilters($baseProducts, $filters, $options);
        $sorted = $this->applySort($filtered, $filters['sort'], $options);
        $paginator = Mbs::paginate($sorted, 12);

        return [
            'products' => $paginator,
            'filters' => $filters,
            'activeCategory' => $filters['category'],
            'categoryMeta' => $this->categoryMeta($filters['category']),
            'catalogAction' => $this->catalogAction($context),
            'catalogContext' => $context,
            'sortOptions' => self::SORT_OPTIONS,
            'categoryCounts' => $this->categoryCounts($baseProducts),
            'clearFiltersUrl' => $this->clearFiltersUrl($context),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function queryActiveProducts(): array
    {
        if (CmsIntegration::usesCmsCatalog()) {
            return array_map(
                fn (array $product) => StorefrontData::enrichProduct($product),
                StorefrontData::allProducts()
            );
        }

        if (Product::query()->exists()) {
            return Product::query()
                ->with(['category', 'brand', 'images'])
                ->where('status', 'active')
                ->get()
                ->map(fn (Product $product) => $this->mapDatabaseProduct($product))
                ->all();
        }

        return StorefrontData::allProducts();
    }

    /**
     * @return array<string, mixed>
     */
    private function contextOptions(string $context, Request $request): array
    {
        return match ($context) {
            'new-arrivals' => [
                'context' => $context,
                'defaultSort' => 'newest',
                'newArrivalsOnly' => true,
                'showNewArrivalsFilter' => false,
                'showFeaturedFilter' => true,
                'lockedCategory' => null,
            ],
            'categories' => [
                'context' => $context,
                'defaultSort' => 'featured',
                'newArrivalsOnly' => false,
                'showNewArrivalsFilter' => false,
                'showFeaturedFilter' => true,
                'lockedCategory' => $request->query('category'),
            ],
            default => [
                'context' => $context,
                'defaultSort' => 'featured',
                'newArrivalsOnly' => false,
                'showNewArrivalsFilter' => true,
                'showFeaturedFilter' => true,
                'lockedCategory' => $request->query('category'),
            ],
        };
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<int, array<string, mixed>>
     */
    private function baseProducts(string $context, array $options): array
    {
        if ($options['newArrivalsOnly'] ?? false) {
            return StorefrontData::newArrivals();
        }

        return $this->queryActiveProducts();
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    private function resolveFilters(Request $request, array $options): array
    {
        $categories = $this->normalizeList($request->query('categories', []));
        $category = trim((string) $request->query('category', ''));

        if ($categories === [] && $category !== '') {
            $categories = [$category];
        }

        if (($options['lockedCategory'] ?? null) && $categories === []) {
            $categories = [(string) $options['lockedCategory']];
        }

        $brands = $this->normalizeList($request->query('brand', $request->query('brands', [])));

        $sort = (string) $request->query('sort', $options['defaultSort'] ?? 'featured');
        $view = $request->query('view');
        $view = in_array($view, ['grid', 'list'], true) ? $view : 'grid';

        return [
            'search' => trim((string) $request->query('search', '')),
            'categories' => $categories,
            'category' => $categories[0] ?? null,
            'brands' => $brands,
            'min_price' => $this->normalizePrice($request->query('min_price')),
            'max_price' => $this->normalizePrice($request->query('max_price')),
            'rating' => $this->normalizeRating($request->query('rating')),
            'featured' => $request->boolean('featured'),
            'new_arrivals' => $request->boolean('new_arrivals'),
            'in_stock' => $request->boolean('in_stock'),
            'on_sale' => $request->boolean('on_sale'),
            'sort' => $this->validateSort($sort),
            'view' => $view,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $products
     * @param  array<string, mixed>  $filters
     * @param  array<string, mixed>  $options
     * @return array<int, array<string, mixed>>
     */
    private function applyFilters(array $products, array $filters, array $options): array
    {
        $featuredSlugs = $this->featuredSlugs();
        $newArrivalSlugs = $this->newArrivalSlugs();

        return array_values(array_filter($products, function (array $product) use ($filters, $featuredSlugs, $newArrivalSlugs): bool {
            if ($filters['search'] !== '') {
                $needle = strtolower($filters['search']);
                $categoryName = StorefrontData::categoryLabel($product['category'] ?? '');
                $haystack = strtolower(implode(' ', array_filter([
                    $product['name'] ?? '',
                    $product['slug'] ?? '',
                    $product['category'] ?? '',
                    $categoryName,
                    $product['sub_category'] ?? '',
                    $product['brand'] ?? '',
                    $product['description'] ?? '',
                ])));

                if (! str_contains($haystack, $needle)) {
                    return false;
                }
            }

            if ($filters['categories'] !== []) {
                if (! in_array($product['category'] ?? '', $filters['categories'], true)) {
                    return false;
                }
            }

            if ($filters['brands'] !== []) {
                $brand = strtolower($product['brand'] ?? '');
                $allowed = array_map('strtolower', $filters['brands']);

                if (! in_array($brand, $allowed, true)) {
                    return false;
                }
            }

            $price = (int) ($product['price'] ?? 0);

            if ($filters['min_price'] !== null && $price < $filters['min_price']) {
                return false;
            }

            if ($filters['max_price'] !== null && $price > $filters['max_price']) {
                return false;
            }

            if ($filters['rating'] !== null) {
                $rating = (float) ($product['rating'] ?? 0);

                if ($rating < $filters['rating']) {
                    return false;
                }
            }

            if ($filters['featured'] && ! $this->isFeatured($product, $featuredSlugs)) {
                return false;
            }

            if ($filters['new_arrivals'] && ! in_array($product['slug'] ?? '', $newArrivalSlugs, true)) {
                return false;
            }

            if ($filters['in_stock'] && ! ($product['in_stock'] ?? true)) {
                return false;
            }

            if ($filters['on_sale'] && empty($product['old_price'])) {
                return false;
            }

            return true;
        }));
    }

    /**
     * @param  array<int, array<string, mixed>>  $products
     * @param  array<string, mixed>  $options
     * @return array<int, array<string, mixed>>
     */
    private function applySort(array $products, string $sort, array $options): array
    {
        $featuredSlugs = array_flip($this->featuredSlugs());
        $newArrivalSlugs = array_flip($this->newArrivalSlugs());
        $catalogOrder = array_flip(array_column(StorefrontData::allProducts(), 'slug'));

        usort($products, function (array $a, array $b) use ($sort, $featuredSlugs, $newArrivalSlugs, $catalogOrder): int {
            return match ($sort) {
                'newest' => ($catalogOrder[$b['slug'] ?? ''] ?? 0) <=> ($catalogOrder[$a['slug'] ?? ''] ?? 0),
                'price_asc' => ((int) ($a['price'] ?? 0)) <=> ((int) ($b['price'] ?? 0)),
                'price_desc' => ((int) ($b['price'] ?? 0)) <=> ((int) ($a['price'] ?? 0)),
                'name_asc' => strcasecmp($a['name'] ?? '', $b['name'] ?? ''),
                'rating_desc' => ((float) ($b['rating'] ?? 0)) <=> ((float) ($a['rating'] ?? 0)),
                default => $this->compareFeatured($a, $b, $featuredSlugs, $newArrivalSlugs, $catalogOrder),
            };
        });

        return $products;
    }

    /**
     * @param  array<string, int>  $featuredSlugs
     * @param  array<string, int>  $newArrivalSlugs
     * @param  array<string, int>  $catalogOrder
     */
    private function compareFeatured(array $a, array $b, array $featuredSlugs, array $newArrivalSlugs, array $catalogOrder): int
    {
        $scoreA = $this->featuredScore($a, $featuredSlugs, $newArrivalSlugs);
        $scoreB = $this->featuredScore($b, $featuredSlugs, $newArrivalSlugs);

        if ($scoreA !== $scoreB) {
            return $scoreB <=> $scoreA;
        }

        $ratingCompare = ((float) ($b['rating'] ?? 0)) <=> ((float) ($a['rating'] ?? 0));

        if ($ratingCompare !== 0) {
            return $ratingCompare;
        }

        return ($catalogOrder[$a['slug'] ?? ''] ?? PHP_INT_MAX) <=> ($catalogOrder[$b['slug'] ?? ''] ?? PHP_INT_MAX);
    }

    /**
     * @param  array<string, int>  $featuredSlugs
     * @param  array<string, int>  $newArrivalSlugs
     */
    private function featuredScore(array $product, array $featuredSlugs, array $newArrivalSlugs): int
    {
        $score = 0;
        $slug = $product['slug'] ?? '';

        if (isset($featuredSlugs[$slug])) {
            $score += 3;
        }

        if (isset($newArrivalSlugs[$slug])) {
            $score += 2;
        }

        if (! empty($product['featured'])) {
            $score += 2;
        }

        if (($product['badge'] ?? null) === 'SALE') {
            $score += 1;
        }

        return $score;
    }

    /**
     * @param  array<int, array<string, mixed>>  $products
     * @return array<string, int>
     */
    private function categoryCounts(array $products): array
    {
        $counts = [];

        foreach ($products as $product) {
            $slug = $product['category'] ?? '';

            if ($slug === '') {
                continue;
            }

            $counts[$slug] = ($counts[$slug] ?? 0) + 1;
        }

        return $counts;
    }

    /**
     * @return array<string, string>|null
     */
    private function categoryMeta(?string $slug): ?array
    {
        if (! $slug) {
            return null;
        }

        foreach (StorefrontData::categories() as $category) {
            if (($category['slug'] ?? '') === $slug) {
                return [
                    'name' => $category['name'] ?? ucwords(str_replace('-', ' ', $slug)),
                    'slug' => $slug,
                    'description' => $category['description'] ?? 'Browse premium products in this category.',
                ];
            }
        }

        return [
            'name' => ucwords(str_replace('-', ' ', $slug)),
            'slug' => $slug,
            'description' => 'Browse premium products in this category.',
        ];
    }

    private function catalogAction(string $context): string
    {
        return match ($context) {
            'new-arrivals' => route('new-arrivals'),
            'categories' => route('categories'),
            default => route('shop'),
        };
    }

    private function clearFiltersUrl(string $context): string
    {
        return $this->catalogAction($context);
    }

    /**
     * @return array<int, string>
     */
    private function featuredSlugs(): array
    {
        return array_values(array_unique(array_filter(array_merge(
            array_column(StorefrontData::dealProducts(), 'slug'),
            array_column(StorefrontData::bestSelling(), 'slug'),
        ))));
    }

    /**
     * @return array<int, string>
     */
    private function newArrivalSlugs(): array
    {
        return array_column(StorefrontData::newArrivals(), 'slug');
    }

    /**
     * @param  array<int, string>  $featuredSlugs
     */
    private function isFeatured(array $product, array $featuredSlugs): bool
    {
        if (in_array($product['slug'] ?? '', $featuredSlugs, true)) {
            return true;
        }

        if (! empty($product['featured'])) {
            return true;
        }

        return in_array($product['badge'] ?? null, ['SALE', 'NEW'], true);
    }

    private function validateSort(string $sort): string
    {
        return array_key_exists($sort, self::SORT_OPTIONS) ? $sort : 'featured';
    }

    /**
     * @return array<int, string>
     */
    private function normalizeList(mixed $value): array
    {
        if (is_string($value)) {
            $value = array_filter(array_map('trim', explode(',', $value)));
        }

        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_map('strval', $value)));
    }

    private function normalizePrice(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $price = (int) $value;

        return $price >= 0 ? $price : null;
    }

    private function normalizeRating(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $rating = (int) $value;

        return in_array($rating, [2, 3, 4], true) ? $rating : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function mapDatabaseProduct(Product $product): array
    {
        return StorefrontData::enrichProduct([
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => (int) $product->price,
            'old_price' => $product->compare_at_price ? (int) $product->compare_at_price : null,
            'image' => $product->images->first()?->path ?? 'placeholder-product.svg',
            'image_alt' => $product->name,
            'category' => $product->category?->slug ?? '',
            'sub_category' => $product->category?->name ?? '',
            'brand' => $product->brand?->name ?? 'MyBestStore',
            'description' => $product->description,
            'weight' => (float) ($product->weight ?? 0),
            'rating' => 4.5,
            'review_count' => 0,
            'featured' => false,
            'in_stock' => true,
        ]);
    }
}
