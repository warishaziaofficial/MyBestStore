<?php

/**
 * Push existing storefront customers into CRM (one-time backfill / test).
 *
 * Usage: php scripts/sync-customers-to-crm.php
 */

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Customer;
use App\Services\CrmContactSync;

if (! config('crm.enabled')) {
    fwrite(STDERR, "Set CRM_SYNC_ENABLED=true in .env first.\n");
    exit(1);
}

$sync = app(CrmContactSync::class);
$ok = 0;
$fail = 0;

foreach (Customer::query()->orderBy('id')->get() as $customer) {
    if ($sync->syncSignup($customer)) {
        $ok++;
        echo "Synced: {$customer->email}\n";
    } else {
        $fail++;
        echo "Failed: {$customer->email}\n";
    }
}

echo "Done. Synced {$ok}, failed {$fail}.\n";
