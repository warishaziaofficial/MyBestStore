<?php



return [

    'methods' => [

        'cash_on_delivery' => [

            'label' => 'Cash on Delivery',

            'driver' => \App\Services\Payments\CodPaymentService::class,

            'active' => true,

        ],

        'jazzcash' => [

            'label' => 'JazzCash',

            'driver' => \App\Services\Payments\JazzCashPaymentService::class,

            'active' => true,

        ],

    ],



    'statuses' => [

        'unpaid',

        'pending',

        'paid',

        'failed',

    ],



    'jazzcash' => [

        'merchant_id' => env('JAZZCASH_MERCHANT_ID'),

        'password' => env('JAZZCASH_PASSWORD'),

        'integrity_salt' => env('JAZZCASH_INTEGRITY_SALT'),

    ],

];


