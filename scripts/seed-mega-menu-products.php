<?php

/**
 * Seed Shop mega-menu products with Unsplash images into CMS Products table.
 *
 * Usage: php scripts/seed-mega-menu-products.php
 */

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Cms\Models\Product;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

if (! Schema::hasTable('Products')) {
    fwrite(STDERR, "Products table missing. Import cms/Products.sql first.\n");
    exit(1);
}

/** @var list<array{section: string, category: string, sub_category: string, name: string, brand: string, price: int, old_price: int|null, unsplash: string, featured: bool, badge: string|null}> */
$catalog = [
    // Premium TVs
    ['section' => 'Premium TVs', 'category' => 'led-tvs', 'sub_category' => 'Smart TV', 'name' => 'Samsung Neo QLED 4K Smart TV', 'brand' => 'Samsung', 'price' => 289999, 'old_price' => 319999, 'unsplash' => 'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?w=900&q=80', 'featured' => true, 'badge' => 'Hot'],
    ['section' => 'Premium TVs', 'category' => 'led-tvs', 'sub_category' => 'OLED TV', 'name' => 'LG OLED evo Smart TV', 'brand' => 'LG', 'price' => 349999, 'old_price' => null, 'unsplash' => 'https://images.unsplash.com/photo-1571410602960-703cbc97d2bc?w=900&q=80', 'featured' => true, 'badge' => 'New'],
    ['section' => 'Premium TVs', 'category' => 'led-tvs', 'sub_category' => 'Google TV', 'name' => 'Sony Bravia 4K Google TV', 'brand' => 'Sony', 'price' => 259999, 'old_price' => 279999, 'unsplash' => 'https://images.unsplash.com/photo-1461151304267-38535e780f79?w=900&q=80', 'featured' => false, 'badge' => null],

    // Premium Audio
    ['section' => 'Premium Audio', 'category' => 'sound-bars', 'sub_category' => 'Soundbar', 'name' => 'Samsung Dolby Atmos Soundbar', 'brand' => 'Samsung', 'price' => 89999, 'old_price' => 99999, 'unsplash' => 'https://images.unsplash.com/photo-1545454675-3531b543be5d?w=900&q=80', 'featured' => true, 'badge' => null],
    ['section' => 'Premium Audio', 'category' => 'audio-equipment', 'sub_category' => 'Headphones', 'name' => 'Sony Wireless Noise Cancelling Headphones', 'brand' => 'Sony', 'price' => 45999, 'old_price' => null, 'unsplash' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=900&q=80', 'featured' => true, 'badge' => 'Sale'],
    ['section' => 'Premium Audio', 'category' => 'sound-bars', 'sub_category' => 'Party Speaker', 'name' => 'JBL PartyBox Speaker', 'brand' => 'JBL', 'price' => 74999, 'old_price' => null, 'unsplash' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=900&q=80', 'featured' => false, 'badge' => null],
    ['section' => 'Premium Audio', 'category' => 'sound-bars', 'sub_category' => 'Bluetooth Speaker', 'name' => 'Bose Bluetooth Speaker', 'brand' => 'Bose', 'price' => 32999, 'old_price' => 36999, 'unsplash' => 'https://images.unsplash.com/photo-1606225450117-3d9c41c96656?w=900&q=80', 'featured' => false, 'badge' => null],

    // Smart Home
    ['section' => 'Smart Home', 'category' => 'air-purifiers', 'sub_category' => 'Air Purifier', 'name' => 'Smart Air Purifier', 'brand' => 'Xiaomi', 'price' => 54999, 'old_price' => null, 'unsplash' => 'https://images.unsplash.com/photo-1631545806608-8ecc32885663?w=900&q=80', 'featured' => true, 'badge' => null],
    ['section' => 'Smart Home', 'category' => 'air-purifiers', 'sub_category' => 'Vacuum', 'name' => 'Robot Vacuum Cleaner', 'brand' => 'Xiaomi', 'price' => 79999, 'old_price' => 89999, 'unsplash' => 'https://images.unsplash.com/photo-1558317374-067fb85f9620?w=900&q=80', 'featured' => false, 'badge' => null],
    ['section' => 'Smart Home', 'category' => 'accessories', 'sub_category' => 'Smart Lock', 'name' => 'Smart Door Lock', 'brand' => 'Samsung', 'price' => 42999, 'old_price' => null, 'unsplash' => 'https://images.unsplash.com/photo-1558002038-1054807a589d?w=900&q=80', 'featured' => false, 'badge' => null],
    ['section' => 'Smart Home', 'category' => 'accessories', 'sub_category' => 'Security', 'name' => 'Smart Security Camera', 'brand' => 'Xiaomi', 'price' => 18999, 'old_price' => null, 'unsplash' => 'https://images.unsplash.com/photo-1557324232-b8917d3d0eb7?w=900&q=80', 'featured' => false, 'badge' => 'New'],

    // Gaming & Tech
    ['section' => 'Gaming & Tech', 'category' => 'accessories', 'sub_category' => 'Gaming Headset', 'name' => 'Gaming Headset', 'brand' => 'Sony', 'price' => 24999, 'old_price' => null, 'unsplash' => 'https://images.unsplash.com/photo-1618366712010-f17ae1097889?w=900&q=80', 'featured' => false, 'badge' => null],
    ['section' => 'Gaming & Tech', 'category' => 'accessories', 'sub_category' => 'Monitor', 'name' => 'Gaming Monitor', 'brand' => 'Samsung', 'price' => 119999, 'old_price' => null, 'unsplash' => 'https://images.unsplash.com/photo-1527443220154-c4f3982e1e38?w=900&q=80', 'featured' => true, 'badge' => null],
    ['section' => 'Gaming & Tech', 'category' => 'accessories', 'sub_category' => 'Controller', 'name' => 'Wireless Gaming Controller', 'brand' => 'Sony', 'price' => 14999, 'old_price' => 17999, 'unsplash' => 'https://images.unsplash.com/photo-1606144042614-b2416e56c213?w=900&q=80', 'featured' => false, 'badge' => null],
    ['section' => 'Gaming & Tech', 'category' => 'accessories', 'sub_category' => 'Keyboard', 'name' => 'Mechanical Keyboard', 'brand' => 'Logitech', 'price' => 19999, 'old_price' => null, 'unsplash' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=900&q=80', 'featured' => false, 'badge' => null],

    // Accessories
    ['section' => 'Accessories', 'category' => 'mobile-accessories', 'sub_category' => 'Charger', 'name' => 'Type-C Fast Charger', 'brand' => 'Samsung', 'price' => 3999, 'old_price' => null, 'unsplash' => 'https://images.unsplash.com/photo-1591290619762-d2a769618547?w=900&q=80', 'featured' => false, 'badge' => null],
    ['section' => 'Accessories', 'category' => 'mobile-accessories', 'sub_category' => 'Wireless Charger', 'name' => 'Wireless Charging Pad', 'brand' => 'Samsung', 'price' => 6999, 'old_price' => 7999, 'unsplash' => 'https://images.unsplash.com/photo-1598557070575-397a69cd2182?w=900&q=80', 'featured' => false, 'badge' => null],
    ['section' => 'Accessories', 'category' => 'mobile-accessories', 'sub_category' => 'Power Bank', 'name' => 'Power Bank', 'brand' => 'Xiaomi', 'price' => 8999, 'old_price' => null, 'unsplash' => 'https://images.unsplash.com/photo-1609091839311-d53658996329?w=900&q=80', 'featured' => false, 'badge' => null],
    ['section' => 'Accessories', 'category' => 'mobile-accessories', 'sub_category' => 'Smartwatch', 'name' => 'Smartwatch', 'brand' => 'Samsung', 'price' => 54999, 'old_price' => 59999, 'unsplash' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=900&q=80', 'featured' => true, 'badge' => 'Hot'],
];

$imageDir = public_path('uploads/cms/mega-menu/'.date('Y/m'));

if (! is_dir($imageDir)) {
    mkdir($imageDir, 0755, true);
}

function downloadImage(string $url, string $dest): bool
{
    $context = stream_context_create([
        'http' => [
            'header' => "User-Agent: MyBestStore-Seed/1.0\r\n",
            'timeout' => 60,
        ],
        'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
    ]);

    $data = @file_get_contents($url, false, $context);

    if ($data === false || strlen($data) < 1000) {
        return false;
    }

    return file_put_contents($dest, $data) !== false;
}

$created = 0;
$updated = 0;
$imageMap = [];

foreach ($catalog as $item) {
    $slug = Str::slug($item['name']);
    $filename = $slug.'.jpg';
    $absolutePath = $imageDir.'/'.$filename;
    $relativePath = 'uploads/cms/mega-menu/'.date('Y/m').'/'.$filename;

    if (! is_file($absolutePath)) {
        echo "Downloading: {$item['name']}...\n";

        if (! downloadImage($item['unsplash'], $absolutePath)) {
            fwrite(STDERR, "  Failed to download image for {$item['name']}\n");
            $relativePath = 'placeholder-product.svg';
        }
    } else {
        echo "Image exists: {$item['name']}\n";
    }

    $imageMap[$item['name']] = is_file($absolutePath) ? $relativePath : 'placeholder-product.svg';

    $payload = [
        'name' => $item['name'],
        'price' => $item['price'],
        'old_price' => $item['old_price'],
        'image' => $imageMap[$item['name']],
        'image_alt' => $item['name'],
        'category' => $item['category'],
        'sub_category' => $item['sub_category'],
        'description' => '<p>'.htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8').' — premium '.htmlspecialchars(strtolower($item['sub_category']), ENT_QUOTES, 'UTF-8').' from '.htmlspecialchars($item['brand'], ENT_QUOTES, 'UTF-8').'. Free delivery on orders above Rs 10,000 across Pakistan.</p>',
        'rating' => round(4.2 + (crc32($slug) % 8) / 10, 1),
        'review_count' => 12 + (crc32($slug) % 180),
        'badge' => $item['badge'],
        'featured' => $item['featured'],
        'brand' => $item['brand'],
        'stock' => 25,
    ];

    $existing = Product::query()->where('slug', $slug)->first();

    if ($existing) {
        $existing->update($payload);
        $updated++;
        echo "  Updated product #{$existing->id}: {$item['name']}\n";
    } else {
        Product::query()->create(array_merge($payload, ['slug' => $slug]));
        $created++;
        echo "  Created: {$item['name']}\n";
    }
}

// Update mega-menu config images to match downloaded files
$configPath = base_path('config/storefront.php');
$config = file_get_contents($configPath);

if ($config !== false) {
    foreach ($catalog as $item) {
        $label = $item['name'];
        $path = $imageMap[$label] ?? null;

        if (! $path || $path === 'placeholder-product.svg') {
            continue;
        }

        // Replace image path next to each product label in premium_categories
        $pattern = "/(\['label' => '".preg_quote($label, '/')."', 'href' => 'shop', 'route' => true, 'image' => ')[^']+(')/";
        $replacement = '$1'.$path.'$2';
        $config = preg_replace($pattern, $replacement, $config, 1, $count);

        if ($count > 0) {
            echo "  Mega menu image updated for: {$label}\n";
        }
    }

    file_put_contents($configPath, $config);
}

echo "\nDone. Created: {$created}, Updated: {$updated}, Total catalog: ".count($catalog)."\n";
