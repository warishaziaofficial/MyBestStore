<?php

/**
 * Quick mail test — run after setting MAIL_PASSWORD in .env:
 *   php scripts/test-mail.php your@email.com
 */

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$to = $argv[1] ?? null;

if (! $to || ! filter_var($to, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Usage: php scripts/test-mail.php recipient@example.com\n");
    exit(1);
}

$password = (string) env('MAIL_PASSWORD', '');

if ($password === '') {
    fwrite(STDERR, "Set MAIL_PASSWORD in .env first (email account password from hosting/cPanel).\n");
    exit(1);
}

try {
    Illuminate\Support\Facades\Mail::raw(
        'MyBestStore mail test — if you received this, SMTP is working.',
        function ($message) use ($to): void {
            $message->to($to)->subject('MyBestStore — email test');
        }
    );

    echo "Test email sent to {$to}\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Send failed: ".$e->getMessage()."\n");
    exit(1);
}
