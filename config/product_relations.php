<?php

return [
    /*
    | Related category cross-sell map (cart "Complete Your Setup" step 4).
    | Keys are cart product category slugs; values are complement categories.
    */
    'cross_sell_categories' => [
        'led-tvs' => ['sound-bars', 'tv-trolly-stand', 'home-theater', 'accessories', 'mobile-accessories'],
        'sound-bars' => ['home-theater', 'audio-equipment', 'led-tvs', 'mobile-accessories', 'accessories'],
        'home-theater' => ['sound-bars', 'led-tvs', 'audio-equipment', 'accessories'],
        'audio-equipment' => ['sound-bars', 'home-theater', 'lp-record', 'lp-records'],
        'mobile-accessories' => ['accessories', 'audio-equipment', 'sound-bars'],
        'accessories' => ['mobile-accessories', 'audio-equipment', 'led-tvs', 'sound-bars'],
        'air-purifiers' => ['air-purifiers', 'accessories', 'home-theater'],
        'tv-trolly-stand' => ['led-tvs', 'accessories', 'mobile-accessories', 'sound-bars'],
        'blu-ray-movies' => ['blu-ray-movies', '4k-moives', 'lp-record', 'lp-records', 'home-theater', 'led-tvs'],
        '4k-moives' => ['4k-moives', 'blu-ray-movies', 'home-theater', 'led-tvs', 'sound-bars'],
        'lp-record' => ['lp-record', 'lp-records', 'blu-ray-movies', 'audio-equipment', 'home-theater'],
        'lp-records' => ['lp-records', 'lp-record', 'blu-ray-movies', 'audio-equipment'],
    ],

    /*
    | Categories where accessory/mobile cable cross-sell is appropriate.
    */
    'accessory_eligible_categories' => [
        'led-tvs',
        'sound-bars',
        'home-theater',
        'audio-equipment',
        'mobile-accessories',
        'accessories',
        'tv-trolly-stand',
    ],
];
