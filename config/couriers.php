<?php

return [
    'couriers' => [
        ['key' => 'tcs', 'name' => 'TCS', 'tracking_url' => 'https://www.tcsexpress.com/track/{tracking}'],
        ['key' => 'leopard', 'name' => 'Leopard Courier', 'tracking_url' => 'https://leopardscourier.com/track/{tracking}'],
        ['key' => 'trax', 'name' => 'Trax', 'tracking_url' => 'https://trax.pk/track/{tracking}'],
        ['key' => 'postex', 'name' => 'PostEx', 'tracking_url' => 'https://postex.pk/tracking/{tracking}'],
        ['key' => 'ups', 'name' => 'UPS', 'tracking_url' => 'https://www.ups.com/track?tracknum={tracking}'],
        ['key' => 'fedex', 'name' => 'FedEx', 'tracking_url' => 'https://www.fedex.com/fedextrack/?trknbr={tracking}'],
        ['key' => 'dhl', 'name' => 'DHL', 'tracking_url' => 'https://www.dhl.com/pk-en/home/tracking.html?tracking-id={tracking}'],
    ],
];
