<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body { margin: 0; padding: 0; background: #f5faff; font-family: "Segoe UI", Arial, sans-serif; color: #082b4f; }
        .wrapper { max-width: 640px; margin: 0 auto; padding: 24px 16px; }
        .card { background: #ffffff; border: 1px solid #cfe3f8; border-radius: 12px; overflow: hidden; }
        .header { padding: 20px 24px; text-align: center; border-bottom: 1px solid #cfe3f8; }
        .header img { max-width: 160px; height: auto; }
        .body { padding: 24px; font-size: 14px; line-height: 1.6; color: #60758c; }
        .body p { margin: 0 0 12px; }
        .body a { color: #005aa7; }
        .footer { padding: 16px 24px; text-align: center; background: #eaf4ff; border-top: 1px solid #cfe3f8; font-size: 13px; color: #60758c; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <div class="header">
            <img src="{{ asset('logo.png') }}" alt="{{ config('app.name') }}">
        </div>
        <div class="body">
            {!! $body !!}
        </div>
        <div class="footer">
            {{ config('app.name') }} — thank you for choosing us.
        </div>
    </div>
</div>
</body>
</html>
