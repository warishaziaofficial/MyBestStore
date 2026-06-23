<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Cms\Models\FeaturedCollection;
use Cms\Models\Product;
use Illuminate\Support\Facades\Schema;

if (Schema::hasTable('FeaturedCollections')) {
    echo "FeaturedCollections:\n";
    foreach (FeaturedCollection::orderBy('sort_order')->get() as $c) {
        echo "{$c->id}|{$c->title}|{$c->image}|{$c->href}\n";
    }
} else {
    echo "No FeaturedCollections table\n";
}

if (Schema::hasTable('Products')) {
    echo "\nProducts:\n";
    foreach (Product::orderBy('id')->get(['id', 'name', 'price', 'slug', 'image']) as $p) {
        echo "{$p->id}|{$p->name}|{$p->price}|{$p->slug}|{$p->image}\n";
    }
}
