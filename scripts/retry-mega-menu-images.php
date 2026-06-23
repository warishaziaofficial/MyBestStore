<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Cms\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/** Verified working Unsplash URLs (re-tested). */
$fixes = [
    'LG OLED evo Smart TV' => 'https://images.unsplash.com/photo-1593784991095-a205069470b6?w=900&q=80&auto=format&fit=crop',
    'Sony Bravia 4K Google TV' => 'https://images.unsplash.com/photo-1593784991095-a205069470b6?w=900&q=80&auto=format&fit=crop',
    'Bose Bluetooth Speaker' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=900&q=80&auto=format&fit=crop',
    'Smart Air Purifier' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=900&q=80&auto=format&fit=crop',
    'Robot Vacuum Cleaner' => 'https://images.unsplash.com/photo-1628177142898-93e36e4e3a50?w=900&q=80&auto=format&fit=crop',
    'Smart Door Lock' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=900&q=80&auto=format&fit=crop',
    'Smart Security Camera' => 'https://images.unsplash.com/photo-1567443024551-f3e3cc2be870?w=900&q=80&auto=format&fit=crop',
    'Gaming Headset' => 'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=900&q=80&auto=format&fit=crop',
    'Gaming Monitor' => 'https://images.unsplash.com/photo-1593640408182-31c70c8268f5?w=900&q=80&auto=format&fit=crop',
    'Wireless Gaming Controller' => 'https://images.unsplash.com/photo-1621259182978-fbf93132d53d?w=900&q=80&auto=format&fit=crop',
    'Type-C Fast Charger' => 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=900&q=80&auto=format&fit=crop',
    'Wireless Charging Pad' => 'https://images.unsplash.com/photo-1556656793-08538906a9f8?w=900&q=80&auto=format&fit=crop',
    'Power Bank' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=900&q=80&auto=format&fit=crop',
];

$dir = public_path('uploads/cms/mega-menu/'.date('Y/m'));
$configPath = base_path('config/storefront.php');
$config = (string) file_get_contents($configPath);
$ok = 0;
$fail = 0;

foreach ($fixes as $name => $url) {
    $slug = Str::slug($name);
    $absolute = $dir.'/'.$slug.'.jpg';
    $relative = 'uploads/cms/mega-menu/'.date('Y/m').'/'.$slug.'.jpg';

    $response = Http::timeout(60)
        ->withHeaders(['User-Agent' => 'MyBestStore-Seed/1.0'])
        ->get($url);

    if (! $response->successful() || strlen($response->body()) < 5000) {
        echo "FAIL ({$response->status()}): {$name}\n";
        $fail++;
        continue;
    }

    file_put_contents($absolute, $response->body());
    Product::query()->where('slug', $slug)->update(['image' => $relative]);

    $pattern = "/(\['label' => '".preg_quote($name, '/')."', 'href' => 'shop', 'route' => true, 'image' => ')[^']+(')/";
    $config = preg_replace($pattern, '$1'.$relative.'$2', $config, 1);

    echo "OK: {$name}\n";
    $ok++;
}

file_put_contents($configPath, $config);
echo "\nImages fixed: {$ok}, failed: {$fail}\n";
