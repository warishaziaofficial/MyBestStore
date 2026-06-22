<?php

return [
    'session_key' => 'mbs_cart',

    'free_shipping_threshold' => (int) env('CART_FREE_SHIPPING_THRESHOLD', 10000),

    'default_shipping' => (int) env('CART_DEFAULT_SHIPPING', 500),

    /*
    |--------------------------------------------------------------------------
    | Discount codes
    |--------------------------------------------------------------------------
    |
    | type: "percent" (percentage off subtotal) or "fixed" (flat amount off)
    |
    */
    'coupons' => [
        'SAVE10' => ['type' => 'percent', 'value' => 10],
        'FLAT500' => ['type' => 'fixed', 'value' => 500],
        'WELCOME15' => ['type' => 'percent', 'value' => 15],
    ],
];
