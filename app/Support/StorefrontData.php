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
            'brand' => $product['brand'] ?? self::guessBrand($product['name'] ?? ''),
            'description' => $product['description'] ?? null,
            'in_stock' => $product['in_stock'] ?? true,
            'weight' => (float) ($product['weight'] ?? 0),
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
        foreach (self::catalog()['allProducts'] ?? [] as $product) {
            if (($product['slug'] ?? '') === $slug) {
                return self::enrichProduct(self::normalizeProduct($product));
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
                $results[] = $product;

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
        $brand = $product['brand'] ?: self::guessBrand($product['name']);

        return array_merge($product, [
            'brand' => $brand,
            'category_name' => $categoryName,
            'short_description' => $product['description'] ?? 'Premium quality electronics with official warranty and fast delivery across Pakistan.',
            'description' => $product['description'] ?? 'Experience premium performance and reliability with '.$product['name'].'. Ideal for modern homes across Pakistan with official warranty support and expert assistance from MyBestStore.',
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
            if ($product['slug'] === $slug) {
                continue;
            }

            if (($product['category'] ?? '') === $category) {
                $related[] = $product;
            }

            if (count($related) >= $limit) {
                break;
            }
        }

        if (count($related) < $limit) {
            foreach (self::allProducts() as $product) {
                if ($product['slug'] === $slug || in_array($product['slug'], array_column($related, 'slug'), true)) {
                    continue;
                }

                $related[] = $product;

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

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function carouselProducts(int $limit = 8): array
    {
        $allowedCategories = [
            'sound-bars',
            'air-purifiers',
            'home-theater',
            'audio-equipment',
            'mobile-accessories',
            'accessories',
        ];

        $pool = array_merge(
            self::soundBars(),
            self::airPurifiers(),
            self::homeTheater(),
            self::allProducts(),
            self::bestSelling(),
            self::newArrivals(),
        );

        $seen = [];
        $candidates = [];

        foreach ($pool as $product) {
            $category = $product['category'] ?? '';
            $slug = $product['slug'] ?? '';

            if ($slug === '' || isset($seen[$slug])) {
                continue;
            }

            if (! in_array($category, $allowedCategories, true) || self::isTvOrLcdProduct($product)) {
                continue;
            }

            $seen[$slug] = true;
            $candidates[] = $product;
        }

        usort(
            $candidates,
            fn (array $a, array $b): int => self::carouselProductScore($b) <=> self::carouselProductScore($a)
        );

        return self::diverseCarouselSelection($candidates, $limit);
    }

    /**
     * @param  array<int, array<string, mixed>>  $candidates
     * @return array<int, array<string, mixed>>
     */
    private static function diverseCarouselSelection(array $candidates, int $limit): array
    {
        $results = [];
        $categoryCounts = [];
        $categoryCaps = [
            'sound-bars' => 3,
            'mobile-accessories' => 3,
            'air-purifiers' => 2,
            'home-theater' => 2,
            'audio-equipment' => 2,
            'accessories' => 2,
        ];

        foreach ($candidates as $product) {
            $category = $product['category'] ?? '';
            $count = $categoryCounts[$category] ?? 0;
            $cap = $categoryCaps[$category] ?? 2;

            if ($count >= $cap) {
                continue;
            }

            $categoryCounts[$category] = $count + 1;
            $results[] = $product;

            if (count($results) >= $limit) {
                break;
            }
        }

        if (count($results) < $limit) {
            foreach ($candidates as $product) {
                if (in_array($product['slug'] ?? '', array_column($results, 'slug'), true)) {
                    continue;
                }

                $results[] = $product;

                if (count($results) >= $limit) {
                    break;
                }
            }
        }

        return $results;
    }

    private static function isTvOrLcdProduct(array $product): bool
    {
        if (($product['category'] ?? '') === 'led-tvs') {
            return true;
        }

        $haystack = strtolower(trim(($product['name'] ?? '').' '.($product['slug'] ?? '')));
        $blocked = [
            ' lcd',
            'lcd-',
            '-lcd',
            'qled',
            ' oled',
            'led tv',
            'smart tv',
            'television',
            'handheld colour tv',
            ' inch tv',
            '4k tv',
        ];

        foreach ($blocked as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    private static function carouselProductScore(array $product): int
    {
        if (self::isTvOrLcdProduct($product)) {
            return -100;
        }

        $name = strtolower($product['name'] ?? '');
        $score = 0;

        foreach ([
            'wireless earbud' => 18,
            'earbud' => 16,
            'headphone' => 16,
            'headset' => 15,
            'heandset' => 15,
            'sound bar' => 14,
            'soundbar' => 14,
            'speaker' => 13,
            'bluetooth' => 10,
            'air purifier' => 12,
            'home theater' => 12,
            'home theatre' => 12,
            'amplifier' => 11,
            'woofer' => 11,
        ] as $term => $points) {
            if (str_contains($name, $term)) {
                $score += $points;
            }
        }

        $categoryBoost = [
            'mobile-accessories' => 6,
            'audio-equipment' => 5,
            'sound-bars' => 8,
            'home-theater' => 7,
            'air-purifiers' => 6,
            'accessories' => 4,
        ];

        $score += $categoryBoost[$product['category'] ?? ''] ?? 0;
        $score += (int) round((float) ($product['rating'] ?? 0));

        return $score;
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
            'products/showcase-soundbar-1.jpg',
            'products/showcase-soundbar-2.jpg',
            'products/showcase-soundbar-3.jpg',
            'products/showcase-soundbar-4.jpg',
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

    public static function premiumCategories(): array
    {
        return config('storefront.premium_categories', []);
    }

    /**
     * @return array<int, array{name: string, image: string}>
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
            'contactCards' => config('storefront.contact_cards', []),
            'contactMap' => config('storefront.contact_map', []),
            'categoryBanners' => config('storefront.category_banners', []),
        ];
    }
}
