<?php

namespace App\Services;

use App\Models\ProductRelation;
use App\Support\CmsIntegration;
use App\Support\StorefrontData;
use Illuminate\Support\Facades\Schema;

class ProductRelationService
{
    public function __construct(
        private readonly ProductSalesAnalytics $sales,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function forProduct(string $slug, string $type, int $limit = 4, array $excludeSlugs = []): array
    {
        $exclude = array_values(array_unique(array_merge($excludeSlugs, [$slug])));
        $products = $this->fromDatabase($slug, $type, $limit, $exclude);

        if (count($products) < $limit) {
            $fallback = $this->fallbackForProduct(
                $slug,
                $type,
                $limit - count($products),
                array_merge($exclude, array_column($products, 'slug'))
            );

            $products = array_merge($products, $fallback);
        }

        return array_slice($products, 0, $limit);
    }

    /**
     * @param  array<int, string>  $cartSlugs
     * @return array<int, array<string, mixed>>
     */
    public function forCart(array $cartSlugs, int $limit = 4): array
    {
        $cartSlugs = array_values(array_unique(array_filter($cartSlugs)));

        if ($cartSlugs === []) {
            return [];
        }

        $cartItems = [];

        foreach ($cartSlugs as $slug) {
            $item = StorefrontData::findBySlug($slug);

            if ($item) {
                $cartItems[] = $item;
            }
        }

        if ($cartItems === []) {
            return [];
        }

        $exclude = $cartSlugs;
        $products = [];

        foreach ($cartSlugs as $slug) {
            $this->mergeUniqueProducts(
                $products,
                $this->fromDatabase($slug, ProductRelation::TYPE_CROSS_SELL, $limit, $exclude),
                $limit,
                $exclude
            );

            if (count($products) >= $limit) {
                return array_slice($products, 0, $limit);
            }
        }

        $cartCategories = array_values(array_unique(array_filter(array_column($cartItems, 'category'))));
        $this->mergeUniqueProducts(
            $products,
            $this->productsInCategories($cartCategories, $exclude, $limit - count($products)),
            $limit,
            $exclude
        );

        if (count($products) >= $limit) {
            return array_slice($products, 0, $limit);
        }

        $cartBrands = array_values(array_unique(array_filter(array_column($cartItems, 'brand'))));
        $this->mergeUniqueProducts(
            $products,
            $this->productsWithBrands($cartBrands, $exclude, $limit - count($products)),
            $limit,
            $exclude
        );

        if (count($products) >= $limit) {
            return array_slice($products, 0, $limit);
        }

        $relatedCategories = $this->relatedCategoriesForCartItems($cartItems);
        $this->mergeUniqueProducts(
            $products,
            $this->productsInCategories($relatedCategories, $exclude, $limit - count($products)),
            $limit,
            $exclude
        );

        if (count($products) >= $limit) {
            return array_slice($products, 0, $limit);
        }

        $this->mergeUniqueProducts(
            $products,
            $this->bestSellingFallback($exclude, $limit - count($products)),
            $limit,
            $exclude
        );

        return array_slice($products, 0, $limit);
    }

    /**
     * @param  array<int, array<string, mixed>>  $products
     * @param  array<int, array<string, mixed>>  $batch
     * @param  array<int, string>  $exclude
     */
    private function mergeUniqueProducts(array &$products, array $batch, int $limit, array &$exclude): void
    {
        foreach ($batch as $product) {
            $slug = $product['slug'] ?? '';

            if ($slug === '' || in_array($slug, $exclude, true)) {
                continue;
            }

            $products[] = $product;
            $exclude[] = $slug;

            if (count($products) >= $limit) {
                break;
            }
        }
    }

    /**
     * @param  array<int, string>  $categories
     * @param  array<int, string>  $exclude
     * @return array<int, array<string, mixed>>
     */
    private function productsInCategories(array $categories, array $exclude, int $limit): array
    {
        if ($limit <= 0 || $categories === []) {
            return [];
        }

        $candidates = [];

        foreach (StorefrontData::allProducts() as $product) {
            if (in_array($product['slug'], $exclude, true)) {
                continue;
            }

            if (! in_array($product['category'] ?? '', $categories, true)) {
                continue;
            }

            $candidates[] = $product;
        }

        usort($candidates, function (array $a, array $b) {
            return ($b['review_count'] ?? 0) <=> ($a['review_count'] ?? 0)
                ?: ($b['rating'] ?? 0) <=> ($a['rating'] ?? 0);
        });

        return array_slice($candidates, 0, $limit);
    }

    /**
     * @param  array<int, string>  $brands
     * @param  array<int, string>  $exclude
     * @return array<int, array<string, mixed>>
     */
    private function productsWithBrands(array $brands, array $exclude, int $limit): array
    {
        if ($limit <= 0 || $brands === []) {
            return [];
        }

        $candidates = [];

        foreach (StorefrontData::allProducts() as $product) {
            if (in_array($product['slug'], $exclude, true)) {
                continue;
            }

            $brand = $product['brand'] ?? '';

            if ($brand === '' || ! in_array($brand, $brands, true)) {
                continue;
            }

            $candidates[] = $product;
        }

        usort($candidates, function (array $a, array $b) {
            return ($b['review_count'] ?? 0) <=> ($a['review_count'] ?? 0)
                ?: ($b['rating'] ?? 0) <=> ($a['rating'] ?? 0);
        });

        return array_slice($candidates, 0, $limit);
    }

    /**
     * @param  array<int, array<string, mixed>>  $cartItems
     * @return array<int, string>
     */
    private function relatedCategoriesForCartItems(array $cartItems): array
    {
        $cartCategories = array_values(array_unique(array_filter(array_column($cartItems, 'category'))));
        $accessoryEligible = config('product_relations.accessory_eligible_categories', []);
        $allowAccessories = array_intersect($cartCategories, $accessoryEligible) !== [];

        $related = [];

        foreach ($cartItems as $item) {
            $category = $item['category'] ?? '';
            $mapped = config("product_relations.cross_sell_categories.{$category}", []);

            foreach ($mapped as $relatedCategory) {
                if (in_array($relatedCategory, $cartCategories, true)) {
                    continue;
                }

                if (! $allowAccessories && in_array($relatedCategory, ['mobile-accessories', 'accessories'], true)) {
                    continue;
                }

                $related[] = $relatedCategory;
            }
        }

        return array_values(array_unique($related));
    }

    /**
     * @param  array<int, string>  $exclude
     * @return array<int, array<string, mixed>>
     */
    private function bestSellingFallback(array $exclude, int $limit): array
    {
        if ($limit <= 0) {
            return [];
        }

        $products = [];

        foreach (StorefrontData::bestSelling() as $product) {
            if (in_array($product['slug'], $exclude, true)) {
                continue;
            }

            $products[] = $product;

            if (count($products) >= $limit) {
                return $products;
            }
        }

        foreach (StorefrontData::allProducts() as $product) {
            if (in_array($product['slug'], $exclude, true)) {
                continue;
            }

            $products[] = $product;

            if (count($products) >= $limit) {
                break;
            }
        }

        return $products;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fromDatabase(string $slug, string $type, int $limit, array $exclude): array
    {
        if (CmsIntegration::usesCmsCatalog() && Schema::hasTable('ProductRelations')) {
            return $this->fromCmsDatabase($slug, $type, $limit, $exclude);
        }

        if (! Schema::hasTable('product_relations')) {
            return [];
        }

        $relations = ProductRelation::query()
            ->where('product_slug', $slug)
            ->where('relation_type', $type)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $products = [];

        foreach ($relations as $relation) {
            if (in_array($relation->related_product_slug, $exclude, true)) {
                continue;
            }

            $product = StorefrontData::findBySlug($relation->related_product_slug);

            if (! $product) {
                continue;
            }

            $products[] = $product;
            $exclude[] = $product['slug'];

            if (count($products) >= $limit) {
                break;
            }
        }

        return $products;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fromCmsDatabase(string $slug, string $type, int $limit, array $exclude): array
    {
        $product = StorefrontData::findBySlug($slug);

        if (! $product || empty($product['id'])) {
            return [];
        }

        $products = [];

        foreach (StorefrontData::productRelationGroups((int) $product['id']) as $group) {
            if (($group['type'] ?? '') !== $type) {
                continue;
            }

            foreach ($group['products'] ?? [] as $related) {
                $relatedSlug = $related['slug'] ?? '';

                if ($relatedSlug === '' || in_array($relatedSlug, $exclude, true)) {
                    continue;
                }

                $products[] = StorefrontData::enrichProduct($related);
                $exclude[] = $relatedSlug;

                if (count($products) >= $limit) {
                    return $products;
                }
            }
        }

        return $products;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fallbackForProduct(string $slug, string $type, int $limit, array $exclude): array
    {
        if ($limit <= 0) {
            return [];
        }

        if ($type === ProductRelation::TYPE_UPSELL) {
            return $this->upsellFallback($slug, $limit, $exclude);
        }

        if ($type === ProductRelation::TYPE_CROSS_SELL) {
            return $this->crossSellFallback($slug, $limit, $exclude);
        }

        return StorefrontData::relatedProducts($slug, StorefrontData::findBySlug($slug)['category'] ?? '', $limit);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function upsellFallback(string $slug, int $limit, array $exclude): array
    {
        $current = StorefrontData::findBySlug($slug);

        if (! $current) {
            return [];
        }

        $category = $current['category'] ?? '';
        $brand = $current['brand'] ?? '';
        $price = (int) ($current['price'] ?? 0);
        $candidates = [];

        foreach (StorefrontData::allProducts() as $product) {
            if (in_array($product['slug'], $exclude, true)) {
                continue;
            }

            $productPrice = (int) ($product['price'] ?? 0);
            $sameCategoryHigher = ($product['category'] ?? '') === $category && $productPrice > $price;
            $sameBrandPremium = $brand !== '' && ($product['brand'] ?? '') === $brand && $productPrice > $price;

            if ($sameCategoryHigher || $sameBrandPremium) {
                $candidates[] = $product;
            }
        }

        usort($candidates, fn (array $a, array $b) => ($b['price'] ?? 0) <=> ($a['price'] ?? 0));

        return array_slice($candidates, 0, $limit);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function crossSellFallback(string $slug, int $limit, array $exclude): array
    {
        $current = StorefrontData::findBySlug($slug);
        $category = $current['category'] ?? '';
        $targetCategories = config("product_relations.cross_sell_categories.{$category}", []);

        $candidates = [];

        foreach (StorefrontData::allProducts() as $product) {
            if (in_array($product['slug'], $exclude, true)) {
                continue;
            }

            if (! in_array($product['category'] ?? '', $targetCategories, true)) {
                continue;
            }

            $candidates[] = $product;
        }

        usort($candidates, fn (array $a, array $b) => ($a['price'] ?? 0) <=> ($b['price'] ?? 0));

        return array_slice($candidates, 0, $limit);
    }

    /**
     * @return array{upsell: array<int, string>, cross_sell: array<int, string>, related: array<int, string>}
     */
    public function selectedSlugsForProduct(string $slug): array
    {
        if (CmsIntegration::usesCmsCatalog() && Schema::hasTable('ProductRelations')) {
            $product = StorefrontData::findBySlug($slug);

            if (! $product || empty($product['id'])) {
                return ['upsell' => [], 'cross_sell' => [], 'related' => []];
            }

            $selected = ['upsell' => [], 'cross_sell' => [], 'related' => []];

            foreach (StorefrontData::productRelationGroups((int) $product['id']) as $group) {
                $type = $group['type'] ?? '';

                if (! isset($selected[$type])) {
                    continue;
                }

                $selected[$type] = array_values(array_filter(array_map(
                    fn (array $item) => $item['slug'] ?? null,
                    $group['products'] ?? []
                )));
            }

            return $selected;
        }

        if (! Schema::hasTable('product_relations')) {
            return ['upsell' => [], 'cross_sell' => [], 'related' => []];
        }

        $grouped = ProductRelation::query()
            ->where('product_slug', $slug)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('relation_type');

        return [
            'upsell' => $grouped->get(ProductRelation::TYPE_UPSELL, collect())->pluck('related_product_slug')->all(),
            'cross_sell' => $grouped->get(ProductRelation::TYPE_CROSS_SELL, collect())->pluck('related_product_slug')->all(),
            'related' => $grouped->get(ProductRelation::TYPE_RELATED, collect())->pluck('related_product_slug')->all(),
        ];
    }

    /**
     * @param  array<string, array<int, string>|null>  $relations
     */
    public function syncForProduct(string $slug, array $relations): void
    {
        if (CmsIntegration::usesCmsCatalog() && Schema::hasTable('ProductRelations')) {
            return;
        }

        if (! Schema::hasTable('product_relations')) {
            return;
        }

        ProductRelation::query()->where('product_slug', $slug)->delete();

        foreach ([ProductRelation::TYPE_UPSELL, ProductRelation::TYPE_CROSS_SELL, ProductRelation::TYPE_RELATED] as $type) {
            $slugs = $relations[$type] ?? [];

            foreach (array_values(array_unique(array_filter($slugs))) as $order => $relatedSlug) {
                if ($relatedSlug === $slug || ! StorefrontData::findBySlug($relatedSlug)) {
                    continue;
                }

                ProductRelation::query()->create([
                    'product_slug' => $slug,
                    'related_product_slug' => $relatedSlug,
                    'relation_type' => $type,
                    'sort_order' => $order,
                ]);
            }
        }
    }
}
