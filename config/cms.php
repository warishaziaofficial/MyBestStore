<?php

return [
    /*
    | When true, new users can register at /cms/register even after the first admin exists.
    | Default false — only the first admin can register via the signup screen (or use Users in CMS).
    */
    'allow_signup' => env('CMS_ALLOW_SIGNUP', false),

    /* Role assigned when signing up while allow_signup is true (not the first user). */
    'registration_role' => env('CMS_REGISTRATION_ROLE', 'editor'),

    /* Products at or below this stock level trigger a low-stock alert. */
    'stock_alert_threshold' => (int) env('CMS_STOCK_ALERT_THRESHOLD', 5),

    /*
    | Comma-separated emails for out-of-stock alerts. When empty, all CMS admin users are used.
    | Email is sent once when a product's stock reaches zero (checkout, CMS edit, social order).
    | Requires MAIL_* configured in .env. No daily scheduled emails.
    */
    'stock_alert_emails' => env('CMS_STOCK_ALERT_EMAILS', ''),

    /* OpenAI — optional AI insights on Reports page */
    'openai_api_key' => env('OPENAI_API_KEY', ''),
    'openai_model' => env('OPENAI_MODEL', 'gpt-4o-mini'),

    /*
    | SMS alerts for out-of-stock, new orders, inquiries.
    | Drivers: log (default), http (POST webhook), twilio
    */
    'sms_driver' => env('SMS_DRIVER', 'log'),
    'sms_admin_phone' => env('SMS_ADMIN_PHONE', ''),
    'sms_webhook_url' => env('SMS_WEBHOOK_URL', ''),
    'twilio_sid' => env('TWILIO_SID', ''),
    'twilio_token' => env('TWILIO_TOKEN', ''),
    'twilio_from' => env('TWILIO_FROM', ''),

    /* Logo shown in CMS sidebar and dashboard (falls back to bundled SVG if missing). */
    'logo' => env('CMS_LOGO', 'logo.png'),
    'logo_fallback' => 'assets/cms/images/mybeststore-logo.svg',
    'logo_fallback_dark' => 'assets/cms/images/mybeststore-logo-dark.svg',
];
