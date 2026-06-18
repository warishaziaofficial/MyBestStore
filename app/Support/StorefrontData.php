<?php

namespace App\Support;

class StorefrontData
{
    private static ?array $catalog = null;

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
            'image_alt' => $product['imageAlt'] ?? $product['name'] ?? 'Product',
            'category' => $product['category'] ?? '',
            'sub_category' => $product['subCategory'] ?? '',
            'rating' => round((float) ($product['rating'] ?? 4.5), 1),
            'review_count' => (int) ($product['reviewCount'] ?? 0),
            'badge' => $badge,
            'featured' => (bool) ($product['featured'] ?? false),
            'brand' => $product['brand'] ?? '',
        ];
    }

    private static function mapProducts(array $items): array
    {
        return array_map(fn (array $p) => self::normalizeProduct($p), $items);
    }

    private static function exports(string $key, array $fallback = []): array
    {
        $exports = self::catalog()['exports'] ?? [];

        return self::mapProducts($exports[$key] ?? $fallback);
    }

    public static function allProducts(): array
    {
        return self::mapProducts(self::catalog()['allProducts'] ?? []);
    }

    public static function products(): array
    {
        return self::allProducts();
    }

    public static function categories(): array
    {
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
        return self::exports('bestSellingProducts');
    }

    public static function newArrivals(): array
    {
        $items = self::exports('newArrivalProducts');

        return count($items) >= 8 ? $items : array_slice(self::allProducts(), 0, 12);
    }

    public static function ledTvs(): array
    {
        return self::exports('ledTvProducts');
    }

    public static function soundBars(): array
    {
        return self::exports('soundBarProducts');
    }

    public static function airPurifiers(): array
    {
        return self::exports('airPurifierProducts');
    }

    public static function homeTheater(): array
    {
        return self::exports('homeTheaterProducts');
    }

    public static function dealProducts(): array
    {
        $featured = self::exports('featuredProducts');

        return count($featured) ? $featured : array_slice(self::bestSelling(), 0, 6);
    }

    public static function showcaseProduct(): ?array
    {
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
        return config('storefront.brands', []);
    }

    public static function blogPosts(): array
    {
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
        return ['LED TV', 'Soundbar', 'Speaker', 'Air Purifier', 'Home Theater', 'Vinyl', 'Accessories'];
    }

    public static function trustItems(): array
    {
        return [
            ['title' => 'Original Products', 'description' => '100% genuine with official warranty', 'icon' => '✓'],
            ['title' => 'Fast Delivery', 'description' => 'Nationwide shipping across Pakistan', 'icon' => '🚚'],
            ['title' => 'Secure Payment', 'description' => 'Safe and encrypted checkout', 'icon' => '🔒'],
            ['title' => '24/7 Support', 'description' => 'Expert help when you need it', 'icon' => '💬'],
        ];
    }

    public static function filterBrands(): array
    {
        return ['Samsung', 'Sony', 'TCL', 'Panasonic', 'Sharp', 'Pioneer', 'Denon', 'JBL', 'Xiaomi'];
    }

    public static function home(): array
    {
        return array_merge(self::shared(), [
            'heroSlides' => config('storefront.hero_slides', []),
            'categoryStrip' => config('storefront.category_strip', []),
            'promoBanners' => config('storefront.promo_banners', []),
            'exploreCategories' => self::exploreCategories(),
            'bestSelling' => self::bestSelling(),
            'ledTvs' => self::ledTvs(),
            'soundBars' => self::soundBars(),
            'airPurifiers' => self::airPurifiers(),
            'homeTheater' => self::homeTheater(),
            'dealProducts' => self::dealProducts(),
            'showcaseProduct' => self::showcaseProduct(),
            'showcaseGallery' => self::showcaseGallery(),
            'featuredCollections' => config('storefront.featured_collections', []),
            'aiFeatures' => config('storefront.ai_features', []),
            'reviews' => config('storefront.reviews', []),
            'faqs' => config('storefront.faqs', []),
            'navigation' => config('storefront.navigation', []),
            'megaMenu' => config('storefront.mega_menu', []),
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
            'megaMenu' => config('storefront.mega_menu', []),
            'contactCards' => config('storefront.contact_cards', []),
            'categoryBanners' => config('storefront.category_banners', []),
        ];
    }
}
