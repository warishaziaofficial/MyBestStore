<?php

return [
    'enabled' => env('CRM_SYNC_ENABLED', false),

    /** Base URL of the CRM app, e.g. http://localhost:3002 or https://crm.example.com/Sales_Marketing_Automated_System */
    'base_url' => rtrim((string) env('CRM_BASE_URL', ''), '/'),

    /** Shared secret — must match ECOMMERCE_INBOUND_API_KEY or CHATWOOT_INBOUND_API_KEY in CRM .env */
    'api_key' => (string) env('CRM_INBOUND_API_KEY', ''),

    'source' => env('CRM_CONTACT_SOURCE', 'MyBestStore Website'),

    'timeout' => (int) env('CRM_SYNC_TIMEOUT', 10),
];
