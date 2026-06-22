<?php

return [
    'free_shipping_threshold' => (int) env('SHIPPING_FREE_THRESHOLD', 10000),

    'fallback_standard_rate' => (int) env('SHIPPING_FALLBACK_STANDARD', 250),

    'fallback_express_extra' => (int) env('SHIPPING_FALLBACK_EXPRESS_EXTRA', 200),

    'weight_unit' => 'kg',

    'weight_tiers' => [
        ['min' => 0, 'max' => 1, 'extra' => 0],
        ['min' => 1, 'max' => 3, 'extra' => 150],
        ['min' => 3, 'max' => 5, 'extra' => 300],
        ['min' => 5, 'max' => null, 'extra' => 500],
    ],

    'methods' => [
        'standard_delivery' => [
            'label' => 'Standard Delivery',
            'estimate' => '3–5 working days',
        ],
        'express_delivery' => [
            'label' => 'Express Delivery',
            'estimate' => '1–2 working days',
        ],
        'free_shipping' => [
            'label' => 'Free Shipping',
            'estimate' => '3–5 working days',
            'description' => 'For orders above Rs 10,000',
        ],
    ],
];
