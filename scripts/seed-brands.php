<?php

/**
 * Seed Brands table with logos for Shop By Brand section.
 *
 * Usage: php scripts/seed-brands.php
 */

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Cms\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

if (! Schema::hasTable('Brands')) {
    fwrite(STDERR, "Brands table missing. Import cms/Brands.sql first.\n");
    exit(1);
}

$sqlFile = base_path('cms/Brands-logo.sql');
if (is_file($sqlFile)) {
    DB::unprepared(file_get_contents($sqlFile));
    echo "Applied cms/Brands-logo.sql\n";
}

/** Demo: polished brand logos for homepage. */
$brands = [
    ['id' => 'samsung', 'name' => 'Samsung', 'logo' => 'assets/images/brands/samsung.svg', 'sort_order' => 1],
    ['id' => 'sony', 'name' => 'Sony', 'logo' => 'assets/images/brands/sony.svg', 'sort_order' => 2],
    ['id' => 'panasonic', 'name' => 'Panasonic', 'logo' => 'assets/images/brands/panasonic.svg', 'sort_order' => 3],
    ['id' => 'tcl', 'name' => 'TCL', 'logo' => 'assets/images/brands/tcl.svg', 'sort_order' => 4],
    ['id' => 'denon', 'name' => 'Denon', 'logo' => 'assets/images/brands/denon.svg', 'sort_order' => 5],
    ['id' => 'pioneer', 'name' => 'Pioneer', 'logo' => 'assets/images/brands/pioneer.svg', 'sort_order' => 6],
];

$created = 0;
$updated = 0;

foreach ($brands as $row) {
    $payload = ['name' => $row['name']];

    if (Schema::hasColumn('Brands', 'logo')) {
        $payload['logo'] = $row['logo'];
    }

    if (Schema::hasColumn('Brands', 'sort_order')) {
        $payload['sort_order'] = $row['sort_order'];
    }

    $existing = Brand::query()->find($row['id']);

    if ($existing) {
        $existing->update($payload);
        $updated++;
        echo "Updated: {$row['name']}\n";
    } else {
        Brand::query()->create(array_merge(['id' => $row['id']], $payload));
        $created++;
        echo "Created: {$row['name']}\n";
    }
}

$keepIds = collect($brands)->pluck('id')->all();
$removed = Brand::query()->whereNotIn('id', $keepIds)->delete();

echo "\nDone. Created: {$created}, Updated: {$updated}, Removed: {$removed}, Active: ".count($brands)."\n";
