<?php

return [
    'enabled' => env('CHATWOOT_ENABLED', false),

    /** Public Chatwoot URL, e.g. http://localhost:3000 or https://chat.example.com */
    'base_url' => rtrim((string) env('CHATWOOT_BASE_URL', 'http://localhost:3000'), '/'),

    /** Website inbox token from Chatwoot → Settings → Inboxes → Configuration */
    'website_token' => (string) env('CHATWOOT_WEBSITE_TOKEN', ''),

    'launcher_title' => env('CHATWOOT_LAUNCHER_TITLE', 'Chat with us'),

    'welcome_title' => env('CHATWOOT_WELCOME_TITLE', 'MyBestStore Support'),

    'welcome_description' => env(
        'CHATWOOT_WELCOME_DESCRIPTION',
        'Ask about products, orders, delivery, or get help from our team.',
    ),

    'position' => env('CHATWOOT_POSITION', 'right'),

    'widget_color' => env('CHATWOOT_WIDGET_COLOR', '#005AA7'),
];
