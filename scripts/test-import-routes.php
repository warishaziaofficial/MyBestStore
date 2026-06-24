<?php

/**
 * Post-import smoke tests for storefront and admin product list.
 */

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product as LaravelProduct;
use App\Support\StorefrontData;
use Cms\Models\Product as CmsProduct;
use Illuminate\Support\Facades\Schema;

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$failed = [];

$slug = CmsProduct::query()->value('slug') ?? LaravelProduct::query()->value('slug');

if (! $slug) {
    fwrite(STDERR, "No products in database — cannot run product detail test.\n");
    exit(1);
}

$categorySlug = '';

if (Schema::hasTable('Products')) {
    $categorySlug = CmsProduct::query()->where('category', '!=', '')->value('category') ?? '';
} else {
    $product = LaravelProduct::query()->with('category')->first();
    $categorySlug = $product?->category?->slug ?? '';
}

$routes = [
    '/' => 'Homepage',
    '/shop' => 'Product listing',
    '/categories' => 'Categories page',
    '/cart' => 'Cart',
    '/wishlist' => 'Wishlist',
    '/compare' => 'Compare',
    '/product/'.$slug => 'Product detail',
    '/admin/products' => 'Admin product list',
];

if ($categorySlug !== '') {
    $routes['/shop?category='.$categorySlug] = 'Category filter';
}

foreach ($routes as $path => $label) {
    try {
        $response = $kernel->handle(Illuminate\Http\Request::create($path, 'GET'));
        $code = $response->getStatusCode();
        $status = $code < 500 ? 'OK' : 'FAIL';
        echo "[{$status}] {$label} ({$path}) => HTTP {$code}\n";

        if ($code >= 500) {
            $failed[] = $path;
        }
    } catch (Throwable $e) {
        echo "[FAIL] {$label} ({$path}) => {$e->getMessage()}\n";
        $failed[] = $path;
    }

    $kernel->terminate(Illuminate\Http\Request::create($path, 'GET'), $response ?? null);
}

$allProducts = StorefrontData::allProducts();
$searchResults = StorefrontData::searchProducts(substr($slug, 0, 4), 5);

echo '[INFO] StorefrontData products: '.count($allProducts)."\n";
echo '[INFO] Search results: '.count($searchResults)."\n";
echo '[INFO] Laravel products: '.LaravelProduct::query()->count()."\n";

if ($allProducts === [] && LaravelProduct::query()->count() === 0) {
    echo "[FAIL] No products available to storefront\n";
    $failed[] = 'storefront-catalog';
}

echo PHP_EOL.($failed === [] ? 'ALL IMPORT TESTS PASSED' : 'FAILED: '.implode(', ', $failed)).PHP_EOL;

exit($failed === [] ? 0 : 1);
