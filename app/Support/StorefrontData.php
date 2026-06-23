<?php

namespace App\Support;

use Cms\Models\BlogCategory;
use Cms\Models\BlogPost;
use Cms\Models\BlogTag;
use Cms\Models\Brand;
use Cms\Models\Category;
use Cms\Models\ContactCard;
use Cms\Models\Faq;
use Cms\Models\FeaturedCollection;
use Cms\Models\FooterSettings;
use Cms\Models\HeroSlide;
use Cms\Models\Product;
use Cms\Models\ProductImage;
use Cms\Models\ProductPlacement;
use Cms\Models\ProductRelation;
use Cms\Models\PromoBanner;
use Cms\Models\Rating;
use Cms\Models\Review;
use Cms\Models\StaticPage;
use Cms\Models\Testimonial;
use Cms\Models\TrustItem;
use Illuminate\Support\Facades\Schema;

class StorefrontData
{
    private static ?array $catalog = null;

    private static function cmsInstalled(): bool
    {
        try {
            return Schema::hasTable('Products');
        } catch (\Throwable) {
            return false;
        }
    }

    private static function useContentFallback(): bool
    {
        return ! self::cmsInstalled() && (bool) config('storefront.allow_dev_fallback', false);
    }

    /** Original storefront theme content when a CMS table exists but has no rows yet. */
    private static function useThemeDefaults(): bool
    {
        return (bool) config('storefront.use_theme_defaults', true);
    }

    private static function orThemeFallback(array $fromDatabase, string $configKey, ?array $inlineFallback = null): array
    {
        if ($fromDatabase !== []) {
            return $fromDatabase;
        }

        if (! self::useThemeDefaults()) {
            return [];
        }

        $fromConfig = config($configKey, []);

        return $fromConfig !== [] ? $fromConfig : ($inlineFallback ?? []);
    }

    private static function useDatabase(): bool
    {
        return self::cmsInstalled();
    }

    private static function catalog(): array
    {
        if (self::$catalog !== null) {
            return self::$catalog;
        }

        $path = base_path('scripts/generated-catalog.json');

        if (! is_file($path)) {
            self::$catalog = ['allProducts' => [], 'exports' => ['categories' => []]];

            return self::$catalog;
        }

        self::$catalog = json_decode(file_get_contents($path), true) ?: [];

        return self::$catalog;
    }

    private static function normalizeProduct(array $product): array
    {
        $price = (int) ($product['price'] ?? 0);
        $compare = isset($product['compareAtPrice']) ? (int) $product['compareAtPrice'] : null;
        $badge = $product['badge'] ?? null;

        if (! $badge && $compare && $compare > $price) {
            $badge = 'SALE';
        }

        if (! $badge && ! empty($product['featured'])) {
            $badge = 'NEW';
        }

        return [
            'id' => $product['id'] ?? $product['slug'] ?? uniqid(),
            'name' => $product['name'] ?? 'Product',
            'slug' => $product['slug'] ?? '',
            'price' => $price,
            'old_price' => $compare && $compare > $price ? $compare : ($product['old_price'] ?? null),
            'image' => $product['image'] ?? 'placeholder-product.svg',
            'image_alt' => $product['imageAlt'] ?? $product['image_alt'] ?? $product['name'] ?? 'Product',
            'category' => $product['category'] ?? '',
            'sub_category' => $product['subCategory'] ?? $product['sub_category'] ?? '',
            'rating' => round((float) ($product['rating'] ?? 4.5), 1),
            'review_count' => (int) ($product['reviewCount'] ?? $product['review_count'] ?? 0),
            'badge' => $badge,
            'featured' => (bool) ($product['featured'] ?? false),
            'brand' => $product['brand'] ?? '',
            'description' => $product['description'] ?? null,
        ];
    }

    private static function mapProductModel(Product $product): array
    {
        $price = (int) $product->price;
        $oldPrice = $product->old_price ? (int) $product->old_price : null;
        $badge = $product->badge;

        if (! $badge && $oldPrice && $oldPrice > $price) {
            $badge = 'SALE';
        }

        if (! $badge && $product->featured) {
            $badge = 'NEW';
        }

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => $price,
            'old_price' => $oldPrice,
            'image' => $product->image,
            'image_alt' => $product->image_alt,
            'category' => $product->category,
            'sub_category' => $product->sub_category,
            'rating' => round((float) $product->rating, 1),
            'review_count' => (int) $product->review_count,
            'badge' => $badge,
            'featured' => (bool) $product->featured,
            'brand' => $product->brand,
            'description' => $product->description,
            'in_stock' => ! Schema::hasColumn('Products', 'stock') || (int) $product->stock > 0,
            'weight' => 0.0,
        ];
    }

    private static function guessBrand(string $name): string
    {
        $brands = ['Samsung', 'Sony', 'TCL', 'Panasonic', 'Sharp', 'Pioneer', 'Denon', 'JBL', 'Xiaomi', 'LG', 'Philips'];

        foreach ($brands as $brand) {
            if (stripos($name, $brand) !== false) {
                return $brand;
            }
        }

        return 'MyBestStore';
    }

    public static function findBySlug(string $slug): ?array
    {
        if (self::useDatabase()) {
            $product = Product::query()->where('slug', $slug)->first();

            return $product ? self::enrichProduct(self::mapProductModel($product)) : null;
        }

        if (self::useContentFallback()) {
            foreach (self::catalog()['allProducts'] ?? [] as $product) {
                if (($product['slug'] ?? '') === $slug) {
                    return self::enrichProduct(self::normalizeProduct($product));
                }
            }
        }

        return null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function searchProducts(string $query, int $limit = 12): array
    {
        $needle = strtolower(trim($query));

        if ($needle === '') {
            return [];
        }

        $results = [];

        foreach (self::allProducts() as $product) {
            $categoryName = self::categoryLabel($product['category'] ?? '');

            $haystack = strtolower(implode(' ', array_filter([
                $product['name'] ?? '',
                $product['slug'] ?? '',
                $product['category'] ?? '',
                $categoryName,
                $product['sub_category'] ?? '',
                $product['brand'] ?? '',
                $product['description'] ?? '',
            ])));

            if (str_contains($haystack, $needle)) {
                $results[] = self::enrichProduct($product);

                if (count($results) >= $limit) {
                    break;
                }
            }
        }

        return $results;
    }

    public static function enrichProduct(array $product): array
    {
        $categoryName = self::categoryLabel($product['category'] ?? '');
        $brand = $product['brand'] ?: self::guessBrand($product['name'] ?? '');

        $galleryImages = self::productImages((int) ($product['id'] ?? 0));
        if ($galleryImages !== []) {
            $product['gallery'] = array_column($galleryImages, 'image');
        }

        return array_merge($product, [
            'brand' => $brand,
            'category_name' => $categoryName,
            'short_description' => $product['description'] ?? 'Premium quality electronics with official warranty and fast delivery across Pakistan.',
            'description' => $product['description'] ?? 'Experience premium performance and reliability with '.($product['name'] ?? 'this product').'. Ideal for modern homes across Pakistan with official warranty support and expert assistance from MyBestStore.',
            'specifications' => [
                'Brand' => $brand,
                'Category' => $categoryName,
                'Sub Category' => $product['sub_category'] ?: 'Electronics',
                'Warranty' => 'Official Manufacturer Warranty',
                'Delivery' => 'Nationwide across Pakistan',
            ],
            'gallery' => self::productGallery($product),
            'in_stock' => $product['in_stock'] ?? true,
        ]);
    }

    public static function categoryLabel(string $slug): string
    {
        foreach (self::categories() as $category) {
            if ($category['slug'] === $slug) {
                return $category['name'];
            }
        }

        return $slug ? ucwords(str_replace('-', ' ', $slug)) : 'Electronics';
    }

    /**
     * @return array<int, string>
     */
    private static function productGallery(array $product): array
    {
        $images = [];

        if (! empty($product['image'])) {
            $images[] = $product['image'];
        }

        $gallery = $product['gallery'] ?? [];
        if (is_string($gallery)) {
            $gallery = [$gallery];
        }

        if (is_array($gallery)) {
            foreach ($gallery as $item) {
                if (is_string($item) && $item !== '') {
                    $images[] = $item;
                }
            }
        }

        $images = array_values(array_unique(array_filter($images)));

        if ($images === []) {
            $images[] = 'placeholder-product.svg';
        }

        return $images;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function relatedProducts(string $slug, string $category, int $limit = 4): array
    {
        $related = [];

        foreach (self::allProducts() as $product) {
            if (($product['slug'] ?? '') === $slug) {
                continue;
            }

            if (($product['category'] ?? '') === $category) {
                $related[] = self::enrichProduct($product);
            }

            if (count($related) >= $limit) {
                break;
            }
        }

        if (count($related) < $limit) {
            foreach (self::allProducts() as $product) {
                if (($product['slug'] ?? '') === $slug || in_array($product['slug'] ?? '', array_column($related, 'slug'), true)) {
                    continue;
                }

                $related[] = self::enrichProduct($product);

                if (count($related) >= $limit) {
                    break;
                }
            }
        }

        return $related;
    }

    private static function mapProducts(array $items): array
    {
        return array_map(fn (array $p) => self::normalizeProduct($p), $items);
    }

    private static function dbProducts(): array
    {
        return Product::query()->orderByDesc('id')->get()
            ->map(fn (Product $p) => self::mapProductModel($p))
            ->all();
    }

    private static function productsByCategory(string $categorySlug): array
    {
        return Product::query()
            ->where('category', $categorySlug)
            ->orderByDesc('id')
            ->get()
            ->map(fn (Product $p) => self::mapProductModel($p))
            ->all();
    }

    private static function exports(string $key, array $fallback = []): array
    {
        $exports = self::catalog()['exports'] ?? [];

        return self::mapProducts($exports[$key] ?? $fallback);
    }

    public static function allProducts(): array
    {
        if (self::useDatabase()) {
            return self::dbProducts();
        }

        if (self::useContentFallback()) {
            return self::mapProducts(self::catalog()['allProducts'] ?? []);
        }

        return [];
    }

    public static function products(): array
    {
        return self::allProducts();
    }

    public static function categories(): array
    {
        if (Schema::hasTable('Categories')) {
            $items = Category::query()->orderBy('name')->get()->map(fn (Category $cat) => [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'count' => (int) $cat->count,
                'image' => $cat->image,
                'image_alt' => $cat->image_alt,
                'description' => $cat->description,
            ])->all();

            if ($items !== []) {
                return $items;
            }
        }

        if (! self::useContentFallback() && ! self::useThemeDefaults()) {
            return [];
        }

        $localMap = config('storefront.category_images', []);
        $items = self::catalog()['categories'] ?? self::catalog()['exports']['categories'] ?? [];

        return array_values(array_map(function (array $cat) use ($localMap) {
            $slug = $cat['slug'] ?? $cat['id'] ?? '';
            $image = $localMap[$slug] ?? $cat['image'] ?? 'placeholder-product.svg';

            return [
                'id' => $cat['id'] ?? $slug,
                'name' => $cat['name'] ?? 'Category',
                'slug' => $slug,
                'count' => (int) ($cat['productCount'] ?? 0),
                'image' => $image,
                'image_alt' => $cat['imageAlt'] ?? $cat['name'] ?? 'Category',
                'description' => $cat['description'] ?? 'Explore premium products in this category.',
            ];
        }, $items));
    }

    public static function exploreCategories(): array
    {
        $preferred = ['led-tvs', 'sound-bars', 'air-purifiers', 'home-theater', 'audio-equipment', 'lp-records'];
        $all = collect(self::categories());

        return $all
            ->sortBy(fn ($c) => array_search($c['slug'], $preferred, true) === false ? 99 : array_search($c['slug'], $preferred, true))
            ->take(6)
            ->values()
            ->all();
    }

    public static function bestSelling(): array
    {
        if (self::useDatabase()) {
            if (Schema::hasTable('ProductPlacements')) {
                $placed = self::productsFromPlacement('featured', 12);
                if (count($placed)) {
                    return $placed;
                }
            }

            return Product::query()->where('featured', true)->orderByDesc('id')->limit(12)->get()
                ->map(fn (Product $p) => self::mapProductModel($p))
                ->all();
        }

        return self::useContentFallback() ? self::exports('bestSellingProducts') : [];
    }

    public static function newArrivals(): array
    {
        if (self::useDatabase()) {
            if (Schema::hasTable('ProductPlacements')) {
                $placed = self::productsFromPlacement('new_arrival', 12);
                if (count($placed)) {
                    return $placed;
                }
            }

            return Product::query()->orderByDesc('created_at')->limit(12)->get()
                ->map(fn (Product $p) => self::mapProductModel($p))
                ->all();
        }

        if (! self::useContentFallback()) {
            return [];
        }

        $items = self::exports('newArrivalProducts');

        return count($items) >= 8 ? $items : array_slice(self::allProducts(), 0, 12);
    }

    private static function productsFromPlacement(string $placement, int $limit): array
    {
        return ProductPlacement::query()
            ->with('product')
            ->where('placement', $placement)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit($limit)
            ->get()
            ->map(fn (ProductPlacement $row) => $row->product ? self::mapProductModel($row->product) : null)
            ->filter()
            ->values()
            ->all();
    }

    public static function productImages(int $productId): array
    {
        if (! Schema::hasTable('ProductImages')) {
            return [];
        }

        return ProductImage::query()
            ->where('product_id', $productId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (ProductImage $image) => [
                'image' => $image->image,
                'alt_text' => $image->alt_text,
            ])->all();
    }

    public static function productRelationGroups(int $productId): array
    {
        if (! Schema::hasTable('ProductRelations')) {
            return [];
        }

        $catalog = collect(self::allProducts())->keyBy('id');
        $sections = [
            'upsell' => ['title' => 'Upgrade Options', 'subtitle' => 'Premium picks you may prefer'],
            'cross_sell' => ['title' => 'Complete Your Setup', 'subtitle' => 'Products that pair well with this item'],
            'related' => ['title' => 'Related Products', 'subtitle' => 'More items you might like'],
            'frequently_bought_together' => ['title' => 'Frequently Bought Together', 'subtitle' => 'Customers often buy these together'],
        ];

        $groups = [];

        foreach ($sections as $type => $meta) {
            $products = ProductRelation::query()
                ->where('product_id', $productId)
                ->where('relation_type', $type)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->pluck('related_product_id')
                ->map(fn (int $id) => $catalog->get($id))
                ->filter()
                ->values()
                ->all();

            if ($products !== []) {
                $groups[] = array_merge($meta, [
                    'type' => $type,
                    'products' => $products,
                ]);
            }
        }

        return $groups;
    }

    public static function staticPage(string $slug): ?array
    {
        if (Schema::hasTable('StaticPages')) {
            $page = StaticPage::query()->where('slug', $slug)->where('is_published', true)->first();

            if ($page) {
                return [
                    'slug' => $page->slug,
                    'title' => $page->title,
                    'body' => $page->body,
                ];
            }

            if (self::useThemeDefaults()) {
                $fallback = config('storefront.static_pages.'.$slug);

                if ($fallback) {
                    return [
                        'slug' => $slug,
                        'title' => $fallback['title'],
                        'body' => $fallback['body'],
                    ];
                }
            }

            return null;
        }

        if (! self::useContentFallback() && ! self::useThemeDefaults()) {
            return null;
        }

        $fallback = config('storefront.static_pages.'.$slug);

        if (! $fallback) {
            return null;
        }

        return [
            'slug' => $slug,
            'title' => $fallback['title'],
            'body' => $fallback['body'],
        ];
    }

    public static function ledTvs(): array
    {
        if (self::useDatabase()) {
            return self::productsByCategory('led-tvs');
        }

        return self::useContentFallback() ? self::exports('ledTvProducts') : [];
    }

    public static function soundBars(): array
    {
        if (self::useDatabase()) {
            return self::productsByCategory('sound-bars');
        }

        return self::useContentFallback() ? self::exports('soundBarProducts') : [];
    }

    public static function airPurifiers(): array
    {
        if (self::useDatabase()) {
            return self::productsByCategory('air-purifiers');
        }

        return self::useContentFallback() ? self::exports('airPurifierProducts') : [];
    }

    public static function homeTheater(): array
    {
        if (self::useDatabase()) {
            return self::productsByCategory('home-theater');
        }

        return self::useContentFallback() ? self::exports('homeTheaterProducts') : [];
    }

    public static function dealProducts(): array
    {
        if (self::useDatabase()) {
            $sale = Product::query()->where('badge', 'SALE')->orderByDesc('id')->limit(6)->get()
                ->map(fn (Product $p) => self::mapProductModel($p))->all();

            if (count($sale)) {
                return $sale;
            }

            return array_slice(self::bestSelling(), 0, 6);
        }

        if (! self::useContentFallback()) {
            return [];
        }

        $featured = self::exports('featuredProducts');

        return count($featured) ? $featured : array_slice(self::bestSelling(), 0, 6);
    }

    public static function showcaseProduct(): ?array
    {
        if (self::useDatabase()) {
            $featured = Product::query()->where('featured', true)->orderByDesc('id')->first();

            if ($featured) {
                return self::mapProductModel($featured);
            }

            return self::soundBars()[0] ?? self::allProducts()[0] ?? null;
        }

        if (! self::useContentFallback()) {
            return null;
        }

        $raw = self::catalog()['exports']['showcaseProduct'] ?? null;

        return $raw ? self::normalizeProduct($raw) : (self::soundBars()[0] ?? self::allProducts()[0] ?? null);
    }

    public static function showcaseGallery(): array
    {
        return [
            'products/showcase-soundbar.jpg',
            self::showcaseProduct()['image'] ?? 'products/showcase-soundbar.jpg',
        ];
    }

    public static function brands(): array
    {
        if (Schema::hasTable('Brands')) {
            $query = Brand::query();

            if (Schema::hasColumn('Brands', 'sort_order')) {
                $query->orderBy('sort_order')->orderBy('name');
            } else {
                $query->orderBy('name');
            }

            $configLogos = collect(config('storefront.brands', []))->keyBy('id');

            $items = $query->get()->map(function (Brand $brand) use ($configLogos) {
                $logo = Schema::hasColumn('Brands', 'logo') ? ($brand->logo ?? '') : '';
                if ($logo === '') {
                    $logo = (string) ($configLogos->get($brand->id)['logo'] ?? '');
                }

                return [
                    'id' => $brand->id,
                    'name' => $brand->name,
                    'logo' => $logo,
                ];
            })->filter(fn (array $brand) => filled($brand['logo'] ?? null) || filled($brand['name']))->values()->all();

            if ($items !== []) {
                return $items;
            }
        }

        if (self::useContentFallback() || self::useThemeDefaults()) {
            return config('storefront.brands', []);
        }

        return [];
    }

    public static function blogPosts(): array
    {
        if (Schema::hasTable('BlogPosts')) {
            $items = BlogPost::query()->orderByDesc('id')->get()->map(fn (BlogPost $post) => [
                'title' => $post->title,
                'slug' => $post->slug,
                'date' => $post->date,
                'category' => $post->category,
                'excerpt' => $post->excerpt,
                'body' => $post->body,
                'image' => $post->image,
                'author' => $post->author,
            ])->all();

            if ($items !== []) {
                return $items;
            }
        }

        if (! self::useContentFallback() && ! self::useThemeDefaults()) {
            return [];
        }

        return [
            [
                'title' => 'How to Choose the Right LED TV for Your Home',
                'slug' => 'how-to-choose-led-tv',
                'date' => 'June 12, 2026',
                'category' => 'Electronics',
                'excerpt' => 'Screen size, resolution, smart features and warranty explained for Pakistani homes.',
                'image' => 'images/blog/qled-tv-guide.jpg',
                'author' => 'MyBestStore Team',
            ],
            [
                'title' => 'Best Sound Bars for Home Entertainment',
                'slug' => 'best-soundbars',
                'date' => 'June 10, 2026',
                'category' => 'Audio & Speakers',
                'excerpt' => 'Top picks for clear vocals, deep bass and cinematic sound in any room.',
                'image' => 'images/blog/home-audio.jpg',
                'author' => 'MyBestStore Team',
            ],
            [
                'title' => 'Air Purifier Buying Guide for Pakistani Homes',
                'slug' => 'air-purifier-buying-guide',
                'date' => 'June 8, 2026',
                'category' => 'Home Appliances',
                'excerpt' => 'CADR ratings, HEPA filters and room-size recommendations explained.',
                'image' => 'images/blog/new-arrivals.jpg',
                'author' => 'MyBestStore Team',
            ],
            [
                'title' => 'How to Build a Home Theater Setup',
                'slug' => 'home-theater-setup',
                'date' => 'June 5, 2026',
                'category' => 'Buying Guides',
                'excerpt' => 'Speakers, AV receivers, seating and room layout tips for cinema at home.',
                'image' => 'banners/home-entertainment.jpg',
                'author' => 'MyBestStore Team',
            ],
            [
                'title' => 'Vinyl Records and Audio Accessories Guide',
                'slug' => 'vinyl-guide',
                'date' => 'June 3, 2026',
                'category' => 'Books & Media',
                'excerpt' => 'Turntables, cartridges and LP care essentials for collectors.',
                'image' => 'images/categories/lp-records.jpg',
                'author' => 'MyBestStore Team',
            ],
            [
                'title' => 'Tips to Maintain Your Electronics',
                'slug' => 'maintenance-tips',
                'date' => 'June 1, 2026',
                'category' => 'Product Tips',
                'excerpt' => 'Simple care routines to extend the life of your devices.',
                'image' => 'banners/smart-home.jpg',
                'author' => 'MyBestStore Team',
            ],
        ];
    }

    public static function blogCategories(): array
    {
        if (Schema::hasTable('BlogCategories')) {
            $items = BlogCategory::query()->orderBy('label')->get()->map(fn (BlogCategory $cat) => [
                'label' => $cat->label,
                'slug' => $cat->slug,
            ])->all();

            if ($items !== []) {
                return $items;
            }
        }

        if (! self::useContentFallback() && ! self::useThemeDefaults()) {
            return [];
        }

        return [
            ['label' => 'Buying Guides', 'slug' => 'buying-guides'],
            ['label' => 'Electronics', 'slug' => 'electronics'],
            ['label' => 'Audio & Speakers', 'slug' => 'audio-speakers'],
            ['label' => 'Home Appliances', 'slug' => 'home-appliances'],
            ['label' => 'Books & Media', 'slug' => 'books-media'],
            ['label' => 'Deals & Offers', 'slug' => 'deals-offers'],
            ['label' => 'Product Tips', 'slug' => 'product-tips'],
        ];
    }

    public static function blogTags(): array
    {
        if (Schema::hasTable('BlogTags')) {
            $items = BlogTag::query()->orderBy('tag')->pluck('tag')->all();

            if ($items !== []) {
                return $items;
            }
        }

        if (self::useContentFallback() || self::useThemeDefaults()) {
            return ['LED TV', 'Soundbar', 'Speaker', 'Air Purifier', 'Home Theater', 'Vinyl', 'Accessories'];
        }

        return [];
    }

    public static function trustItems(): array
    {
        $inline = [
            ['title' => 'Original Products', 'description' => '100% genuine with official warranty', 'icon' => '✓'],
            ['title' => 'Fast Delivery', 'description' => 'Nationwide shipping across Pakistan', 'icon' => '🚚'],
            ['title' => 'Secure Payment', 'description' => 'Safe and encrypted checkout', 'icon' => '🔒'],
            ['title' => '24/7 Support', 'description' => 'Expert help when you need it', 'icon' => '💬'],
        ];

        if (Schema::hasTable('TrustItems')) {
            $items = TrustItem::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (TrustItem $item) => [
                    'title' => $item->title,
                    'description' => $item->description,
                    'icon' => $item->icon,
                ])->all();

            return self::orThemeFallback($items, 'storefront.trust_items', $inline);
        }

        if (self::useContentFallback() || self::useThemeDefaults()) {
            return $inline;
        }

        return [];
    }

    public static function promoBanners(): array
    {
        if (Schema::hasTable('PromoBanners')) {
            $items = PromoBanner::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (PromoBanner $banner) => [
                    'label' => $banner->label,
                    'title' => $banner->title,
                    'image' => $banner->image,
                    'href' => $banner->href,
                ])->all();

            if ($items !== []) {
                return $items;
            }
        }

        if (! self::useContentFallback() && ! self::useThemeDefaults()) {
            return [];
        }

        return array_map(static function (array $banner): array {
            return [
                'label' => $banner['label'] ?? 'Offer',
                'title' => $banner['title'] ?? '',
                'image' => $banner['image'] ?? '',
                'href' => $banner['href'] ?? 'shop',
            ];
        }, config('storefront.promo_banners', []));
    }

    public static function featuredCollections(): array
    {
        if (Schema::hasTable('FeaturedCollections')) {
            $items = FeaturedCollection::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(function (FeaturedCollection $collection) {
                    $item = [
                        'title' => $collection->title,
                        'subtitle' => $collection->subtitle,
                        'image' => $collection->image,
                        'href' => $collection->href,
                    ];

                    if (Schema::hasColumn('FeaturedCollections', 'product_slug') && filled($collection->product_slug ?? null)) {
                        $item['slug'] = $collection->product_slug;
                    }

                    if (Schema::hasColumn('FeaturedCollections', 'price') && filled($collection->price ?? null)) {
                        $item['price'] = (int) $collection->price;
                    }

                    return $item;
                })->all();

            return self::orThemeFallback($items, 'storefront.featured_collections');
        }

        if (self::useContentFallback() || self::useThemeDefaults()) {
            return config('storefront.featured_collections', []);
        }

        return [];
    }

    public static function contactCards(): array
    {
        if (Schema::hasTable('ContactCards')) {
            $items = ContactCard::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (ContactCard $card) => [
                    'title' => $card->title,
                    'value' => $card->value,
                    'description' => $card->description,
                ])->all();

            return self::orThemeFallback($items, 'storefront.contact_cards');
        }

        if (self::useContentFallback() || self::useThemeDefaults()) {
            return config('storefront.contact_cards', []);
        }

        return [];
    }

    public static function footerSettings(): array
    {
        if (Schema::hasTable('FooterSettings')) {
            $settings = FooterSettings::stored();

            if ($settings) {
                return [
                    'tagline' => $settings->tagline,
                    'website_url' => $settings->website_url,
                    'instagram_url' => $settings->instagram_url,
                    'facebook_url' => $settings->facebook_url,
                    'copyright_text' => $settings->copyright_text,
                    'newsletter_heading' => $settings->newsletter_heading,
                    'newsletter_text' => $settings->newsletter_text,
                ];
            }

            if (self::useThemeDefaults()) {
                return [
                    'tagline' => 'The premium e-commerce destination in Pakistan. Quality electronics, appliances and audio with nationwide support.',
                    'website_url' => 'https://mybeststore.pk/',
                    'instagram_url' => null,
                    'facebook_url' => null,
                    'copyright_text' => 'MyBestStore.pk — All rights reserved.',
                    'newsletter_heading' => 'Newsletter',
                    'newsletter_text' => 'Subscribe for special offers, new arrivals and exclusive deals.',
                ];
            }

            return FooterSettings::emptyDefaults();
        }

        if (! self::useContentFallback() && ! self::useThemeDefaults()) {
            return FooterSettings::emptyDefaults();
        }

        return [
            'tagline' => 'The premium e-commerce destination in Pakistan. Quality electronics, appliances and audio with nationwide support.',
            'website_url' => 'https://mybeststore.pk/',
            'instagram_url' => null,
            'facebook_url' => null,
            'copyright_text' => 'MyBestStore.pk — All rights reserved.',
            'newsletter_heading' => 'Newsletter',
            'newsletter_text' => 'Subscribe for special offers, new arrivals and exclusive deals.',
        ];
    }

    public static function premiumCategories(): array
    {
        return config('storefront.premium_categories', []);
    }

    /**
     * @return array<int, array{name: string, image: string, href?: string, route?: bool}>
     */
    public static function premiumCategoryTiles(): array
    {
        return array_map(fn (array $category) => [
            'name' => $category['title'],
            'image' => $category['image'] ?? 'placeholder-product.svg',
            'href' => $category['href'] ?? 'shop',
            'route' => $category['route'] ?? true,
        ], self::premiumCategories());
    }

    public static function megaMenu(): array
    {
        $menu = config('storefront.mega_menu', []);

        return count($menu) ? $menu : self::premiumCategories();
    }

    public static function carouselProducts(int $limit = 8): array
    {
        $pool = self::newArrivals() ?: self::bestSelling() ?: self::allProducts();

        return array_slice($pool, 0, $limit);
    }

    public static function filterBrands(): array
    {
        if (self::useDatabase()) {
            $fromProducts = Product::query()->distinct()->orderBy('brand')->pluck('brand')->filter()->values()->all();

            if ($fromProducts !== []) {
                return $fromProducts;
            }

            if (self::useThemeDefaults()) {
                return collect(config('storefront.brands', []))->pluck('name')->filter()->values()->all();
            }

            return [];
        }

        return self::useContentFallback()
            ? ['Samsung', 'Sony', 'TCL', 'Panasonic', 'Sharp', 'Pioneer', 'Denon', 'JBL', 'Xiaomi']
            : [];
    }

    public static function heroSlides(): array
    {
        if (Schema::hasTable('HeroSlides')) {
            $items = HeroSlide::query()->orderBy('id')->get()->map(fn (HeroSlide $slide) => [
                'image' => $slide->image,
                'eyebrow' => $slide->eyebrow,
                'title' => $slide->title,
                'subtitle' => $slide->subtitle,
                'cta' => $slide->cta,
                'cta_href' => $slide->cta_href,
                'secondary' => $slide->secondary,
                'secondary_href' => $slide->secondary_href,
            ])->all();

            return self::orThemeFallback($items, 'storefront.hero_slides');
        }

        if (self::useContentFallback() || self::useThemeDefaults()) {
            return config('storefront.hero_slides', []);
        }

        return [];
    }

    public static function testimonials(): array
    {
        if (Schema::hasTable('Testimonials')) {
            $items = Testimonial::query()
                ->where('is_featured', true)
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->get()
                ->map(fn (Testimonial $item) => [
                    'name' => $item->name,
                    'rating' => (int) $item->rating,
                    'text' => $item->text,
                ])->all();

            return self::orThemeFallback($items, 'storefront.reviews');
        }

        if (self::useContentFallback() || self::useThemeDefaults()) {
            return config('storefront.reviews', []);
        }

        return [];
    }

    /** Homepage section — alias kept for existing Blade views. */
    public static function reviews(): array
    {
        return self::testimonials();
    }

    public static function productRatings(int $productId, int $limit = 20): array
    {
        if (! Schema::hasTable('Ratings')) {
            return [];
        }

        return Rating::query()
            ->where('product_id', $productId)
            ->where('status', 'approved')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(fn (Rating $rating) => [
                'id' => $rating->id,
                'reviewer_name' => $rating->reviewer_name,
                'rating' => (int) $rating->rating,
                'created_at' => $rating->created_at?->toDateTimeString(),
            ])->all();
    }

    public static function productReviews(int $productId, int $limit = 20): array
    {
        if (! Schema::hasTable('Reviews')) {
            return [];
        }

        return Review::query()
            ->where('product_id', $productId)
            ->where('status', 'approved')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(fn (Review $review) => [
                'id' => $review->id,
                'reviewer_name' => $review->reviewer_name,
                'title' => $review->title,
                'text' => $review->text,
                'created_at' => $review->created_at?->toDateTimeString(),
            ])->all();
    }

    public static function faqs(): array
    {
        if (Schema::hasTable('Faqs')) {
            $items = Faq::query()->orderBy('id')->get()->map(fn (Faq $faq) => [
                'q' => $faq->q,
                'a' => $faq->a,
            ])->all();

            return self::orThemeFallback($items, 'storefront.faqs');
        }

        if (self::useContentFallback() || self::useThemeDefaults()) {
            return config('storefront.faqs', []);
        }

        return [];
    }

    public static function home(): array
    {
        return array_merge(self::shared(), [
            'heroSlides' => self::heroSlides(),
            'categoryStrip' => config('storefront.category_strip', []),
            'promoBanners' => self::promoBanners(),
            'exploreCategories' => self::exploreCategories(),
            'bestSelling' => self::bestSelling(),
            'ledTvs' => self::ledTvs(),
            'soundBars' => self::soundBars(),
            'airPurifiers' => self::airPurifiers(),
            'homeTheater' => self::homeTheater(),
            'dealProducts' => self::dealProducts(),
            'showcaseProduct' => self::showcaseProduct(),
            'showcaseGallery' => self::showcaseGallery(),
            'featuredCollections' => self::featuredCollections(),
            'aiFeatures' => config('storefront.ai_features', []),
            'reviews' => self::reviews(),
            'faqs' => self::faqs(),
            'navigation' => config('storefront.navigation', []),
            'megaMenu' => self::megaMenu(),
            'premiumCategories' => self::premiumCategoryTiles(),
        ]);
    }

    public static function shared(): array
    {
        return [
            'products' => self::allProducts(),
            'categories' => self::categories(),
            'posts' => self::blogPosts(),
            'brands' => self::brands(),
            'blogCategories' => self::blogCategories(),
            'blogTags' => self::blogTags(),
            'trustItems' => self::trustItems(),
            'newArrivals' => self::newArrivals(),
            'filterBrands' => self::filterBrands(),
            'navigation' => config('storefront.navigation', []),
            'megaMenu' => self::megaMenu(),
            'contactCards' => self::contactCards(),
            'footerSettings' => self::footerSettings(),
            'categoryBanners' => config('storefront.category_banners', []),
        ];
    }
}
