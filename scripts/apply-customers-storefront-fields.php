<?php

/**
 * Add name/phone/remember_token to CMS Customers table for storefront signup.
 *
 * Usage: php scripts/apply-customers-storefront-fields.php
 */

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

if (! Schema::hasTable('Customers')) {
    fwrite(STDERR, "Customers table missing. Import cms/Customers.sql first.\n");
    exit(1);
}

$changes = [];

if (! Schema::hasColumn('Customers', 'name')) {
    DB::statement('ALTER TABLE Customers ADD COLUMN name VARCHAR(120) NULL AFTER id');
    $changes[] = 'name';
}

if (! Schema::hasColumn('Customers', 'phone')) {
    DB::statement('ALTER TABLE Customers ADD COLUMN phone VARCHAR(30) NULL AFTER email');
    $changes[] = 'phone';
}

if (! Schema::hasColumn('Customers', 'remember_token')) {
    DB::statement('ALTER TABLE Customers ADD COLUMN remember_token VARCHAR(100) NULL AFTER password');
    $changes[] = 'remember_token';
}

if ($changes === []) {
    echo "Customers table already has storefront fields.\n";
    exit(0);
}

echo 'Added columns: '.implode(', ', $changes)."\n";
