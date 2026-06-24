<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$downloads = [
    [
        'url' => 'https://digitalwares.pk/data/product/zebra-zt410-barcode-zebra-pakistan.jpg',
        'dest' => public_path('assets/images/hero/hero-digital-hardware-product.jpg'),
    ],
    [
        'url' => 'https://digitalwares.pk/data/category/Harbortouchterminal.jpg',
        'dest' => public_path('assets/images/hero/digitalwares-point-of-sales.jpg'),
    ],
];

$context = stream_context_create([
    'http' => [
        'header' => "User-Agent: MyBestStore-Import/1.0\r\nAccept: */*\r\n",
        'timeout' => 45,
        'ignore_errors' => true,
    ],
]);

$dir = public_path('assets/images/hero');
if (! is_dir($dir)) {
    mkdir($dir, 0755, true);
}

foreach ($downloads as $item) {
    $data = @file_get_contents($item['url'], false, $context);
    if ($data === false || strlen($data) < 5000) {
        fwrite(STDERR, 'Failed: '.$item['url'].PHP_EOL);
        exit(1);
    }
    file_put_contents($item['dest'], $data);
    echo 'Saved '.basename($item['dest']).' ('.strlen($data)." bytes)\n";
}

$timeAttendanceSource = public_path('uploads/cms/2026/06/zkteco-k40-0.jpg');
$timeAttendanceDest = public_path('assets/images/hero/digitalwares-access-control.jpg');

if (! is_file($timeAttendanceSource)) {
    fwrite(STDERR, "Missing access control source image\n");
    exit(1);
}

copy($timeAttendanceSource, $timeAttendanceDest);
echo 'Saved '.basename($timeAttendanceDest).' from catalog product image'."\n";

$legacyTimeAttendanceDest = public_path('assets/images/hero/digitalwares-time-attendance.jpg');
copy($timeAttendanceSource, $legacyTimeAttendanceDest);
echo 'Saved '.basename($legacyTimeAttendanceDest).' from catalog product image'."\n";
