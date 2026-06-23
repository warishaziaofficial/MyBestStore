<?php

/**
 * Add electronics products to Curated Collections (FeaturedCollections).
 *
 * Usage: php scripts/seed-featured-collections.php
 */

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Cms\Models\FeaturedCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

if (! Schema::hasTable('FeaturedCollections')) {
    fwrite(STDERR, "FeaturedCollections table missing. Import cms/FeaturedCollections.sql first.\n");
    exit(1);
}

$sqlFile = base_path('cms/FeaturedCollections-product-fields.sql');
if (is_file($sqlFile)) {
    try {
        DB::unprepared(file_get_contents($sqlFile));
        echo "Applied cms/FeaturedCollections-product-fields.sql\n";
    } catch (Throwable $e) {
        // MySQL < 8 may not support IF NOT EXISTS on ADD COLUMN; apply manually if needed.
        if (! Schema::hasColumn('FeaturedCollections', 'price')) {
            DB::statement('ALTER TABLE FeaturedCollections ADD COLUMN price INT NULL AFTER href');
        }
        if (! Schema::hasColumn('FeaturedCollections', 'product_slug')) {
            DB::statement('ALTER TABLE FeaturedCollections ADD COLUMN product_slug VARCHAR(255) NULL AFTER price');
        }
    }
}

/** @var list<array{title: string, subtitle: string, image: string, product_slug: string, price: int, sort_order: int}> */
$products = [
    [
        'title' => 'Samsung Neo QLED TV',
        'subtitle' => 'Premium LED TV',
        'image' => 'uploads/cms/mega-menu/2026/06/samsung-neo-qled-4k-smart-tv.jpg',
        'product_slug' => 'samsung-neo-qled-4k-smart-tv',
        'price' => 289999,
        'sort_order' => 3,
    ],
    [
        'title' => 'Sony Wireless Headphones',
        'subtitle' => 'Premium Audio',
        'image' => 'uploads/cms/mega-menu/2026/06/sony-wireless-noise-cancelling-headphones.jpg',
        'product_slug' => 'sony-wireless-noise-cancelling-headphones',
        'price' => 45999,
        'sort_order' => 4,
    ],
];

$created = 0;
$updated = 0;

foreach ($products as $item) {
    $existing = FeaturedCollection::query()
        ->where('product_slug', $item['product_slug'])
        ->first();

    $payload = [
        'title' => $item['title'],
        'subtitle' => $item['subtitle'],
        'image' => $item['image'],
        'href' => 'shop',
        'product_slug' => $item['product_slug'],
        'price' => $item['price'],
        'sort_order' => $item['sort_order'],
        'is_active' => true,
    ];

    if ($existing) {
        $existing->update($payload);
        $updated++;
        echo "Updated: {$item['title']}\n";
    } else {
        FeaturedCollection::query()->create($payload);
        $created++;
        echo "Created: {$item['title']}\n";
    }
}

echo "Done. Created {$created}, updated {$updated}.\n";
