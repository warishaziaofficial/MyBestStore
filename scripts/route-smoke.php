<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$slug = Cms\Models\Product::query()->value('slug') ?? 'test';

$routes = [
    '/',
    '/shop',
    '/categories',
    '/new-arrivals',
    '/blog',
    '/contact',
    '/cart',
    '/checkout',
    '/track-order',
    '/wishlist',
    '/compare',
    '/cms/login',
    '/product/'.$slug,
];

$failed = [];

foreach ($routes as $path) {
    try {
        $response = $kernel->handle(Illuminate\Http\Request::create($path, 'GET'));
        $code = $response->getStatusCode();
        echo $path.' => '.$code.PHP_EOL;

        if ($code >= 500) {
            $body = $response->getContent();
            if (preg_match('/Undefined variable \$(\w+)/', $body, $m)) {
                echo '  ERROR: Undefined variable $'.$m[1].PHP_EOL;
            } elseif (preg_match('/Base table or view not found[^<]*/', $body, $m)) {
                echo '  ERROR: '.trim(strip_tags($m[0])).PHP_EOL;
            } else {
                echo '  ERROR: HTTP '.$code.PHP_EOL;
            }
            $failed[] = $path;
        }
    } catch (Throwable $e) {
        echo $path.' => EXCEPTION: '.$e->getMessage().PHP_EOL;
        $failed[] = $path;
    }

    $kernel->terminate(Illuminate\Http\Request::create($path, 'GET'), $response ?? null);
}

echo PHP_EOL.($failed === [] ? 'ALL OK' : 'FAILED: '.implode(', ', $failed)).PHP_EOL;
