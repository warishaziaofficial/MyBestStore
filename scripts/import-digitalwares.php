<?php

/**
 * Import categories, products, images, and logo from digitalwares.pk into CMS tables.
 *
 * Usage:
 *   php scripts/import-digitalwares.php --dry-run
 *   php scripts/import-digitalwares.php
 *   php scripts/import-digitalwares.php --skip-images
 *   php scripts/import-digitalwares.php --limit=10
 *   php scripts/import-digitalwares.php --test
 */

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

require_once __DIR__.'/lib/ImportLogger.php';
require_once __DIR__.'/lib/CmsSchemaBootstrap.php';
require_once __DIR__.'/lib/CatalogWriter.php';
require_once __DIR__.'/lib/DigitalwaresImporter.php';

use Scripts\Lib\DigitalwaresImporter;
use Scripts\Lib\ImportLogger;

$options = getopt('', ['dry-run', 'skip-images', 'limit:', 'no-backup', 'test', 'help']);

if (isset($options['help'])) {
    echo <<<HELP
Import catalog data from digitalwares.pk into CMS tables (Categories, Products, ProductImages).

Options:
  --dry-run       Preview creates/updates without writing to the database
  --skip-images   Skip downloading product images and logo
  --limit=N       Import only the first N products (for testing)
  --no-backup     Skip SQLite backup before import
  --test          Run storefront smoke tests after import
  --help          Show this help

Examples:
  php scripts/import-digitalwares.php --dry-run
  php scripts/import-digitalwares.php
  php scripts/import-digitalwares.php --limit=5 --test

HELP;
    exit(0);
}

$dryRun = isset($options['dry-run']);
$skipImages = isset($options['skip-images']);
$limit = isset($options['limit']) ? (int) $options['limit'] : 0;
$runTests = isset($options['test']);
$noBackup = isset($options['no-backup']);

echo "MyBestStore — digitalwares.pk import\n";
echo 'Mode: '.($dryRun ? 'DRY RUN' : 'LIVE')."\n";
echo 'Images: '.($skipImages ? 'skip' : 'download')."\n";

if ($limit > 0) {
    echo "Limit: {$limit} products\n";
}

if (! $dryRun && ! $noBackup) {
    try {
        $backupPath = DigitalwaresImporter::backupDatabase();

        if ($backupPath) {
            echo "Database backup: {$backupPath}\n";
        } else {
            echo "No SQLite database file found — backup skipped.\n";
        }
    } catch (Throwable $e) {
        fwrite(STDERR, "Backup failed: {$e->getMessage()}\n");
        exit(1);
    }
}

$logger = new ImportLogger;
$importer = new DigitalwaresImporter($logger, $dryRun, $skipImages, $limit);

try {
    $importer->run();
} catch (Throwable $e) {
    fwrite(STDERR, 'Import failed: '.$e->getMessage()."\n");
    exit(1);
}

$logger->summary($dryRun ? 'Dry-run summary' : 'Import summary');

if ($runTests && ! $dryRun) {
    echo "\nRunning post-import smoke tests...\n";
    passthru(PHP_BINARY.' '.escapeshellarg(__DIR__.'/test-import-routes.php'), $exitCode);

    if ($exitCode !== 0) {
        exit($exitCode);
    }
}

exit(0);
