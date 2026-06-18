<?php

return [
    'navigation' => [
        ['label' => 'Home', 'href' => 'home', 'route' => true],
        ['label' => 'Shop', 'href' => 'shop', 'route' => true, 'mega' => true],
        ['label' => 'Deals', 'href' => 'shop', 'route' => true, 'hash' => '#deals'],
        ['label' => 'New Arrivals', 'href' => 'new-arrivals', 'route' => true],
        ['label' => 'Categories', 'href' => 'categories', 'route' => true],
        ['label' => 'Blog', 'href' => 'blog', 'route' => true],
        ['label' => 'Contact', 'href' => 'contact', 'route' => true],
    ],

    'mega_menu' => [
        [
            'title' => 'Electronics',
            'links' => [
                ['label' => 'LED TVs', 'href' => 'shop', 'route' => true],
                ['label' => 'Home Theater', 'href' => 'shop', 'route' => true],
                ['label' => 'Audio Equipment', 'href' => 'shop', 'route' => true],
                ['label' => 'Smart Gadgets', 'href' => 'shop', 'route' => true],
            ],
        ],
        [
            'title' => 'Audio & Speakers',
            'links' => [
                ['label' => 'Sound Bars', 'href' => 'shop', 'route' => true],
                ['label' => 'Bluetooth Speakers', 'href' => 'shop', 'route' => true],
                ['label' => 'Home Audio', 'href' => 'shop', 'route' => true],
                ['label' => 'Accessories', 'href' => 'shop', 'route' => true],
            ],
        ],
        [
            'title' => 'Home Appliances',
            'links' => [
                ['label' => 'Air Purifiers', 'href' => 'shop', 'route' => true],
                ['label' => 'Kitchen Appliances', 'href' => 'shop', 'route' => true],
                ['label' => 'TV Stands', 'href' => 'shop', 'route' => true],
                ['label' => 'Mobile Accessories', 'href' => 'shop', 'route' => true],
            ],
        ],
        [
            'title' => 'Customer Support',
            'links' => [
                ['label' => 'Contact Us', 'href' => 'contact', 'route' => true],
                ['label' => 'Warranty', 'href' => 'contact', 'route' => true],
                ['label' => 'Returns & Exchanges', 'href' => 'contact', 'route' => true],
                ['label' => 'FAQs', 'href' => 'home', 'route' => true, 'hash' => '#faq'],
            ],
        ],
    ],

    'category_strip' => [
        ['label' => 'Electronics', 'icon' => '📺', 'href' => 'categories'],
        ['label' => 'Home Appliances', 'icon' => '🏠', 'href' => 'categories'],
        ['label' => 'Audio & Speakers', 'icon' => '🔊', 'href' => 'categories'],
        ['label' => 'Books & Media', 'icon' => '💿', 'href' => 'categories'],
        ['label' => 'Accessories', 'icon' => '🔌', 'href' => 'categories'],
        ['label' => 'Deals', 'icon' => '🏷️', 'href' => 'shop', 'hash' => '#deals'],
    ],

    'hero_slides' => [
        [
            'image' => 'hero/hero-main.jpg',
            'eyebrow' => 'Premium Electronics Store',
            'title' => 'Discover Premium Electronics For Every Lifestyle',
            'subtitle' => 'Shop LED TVs, sound bars, air purifiers, home theater and more — delivered across Pakistan.',
            'cta' => 'Shop Now',
            'cta_href' => 'shop',
            'secondary' => 'New Arrivals',
            'secondary_href' => 'new-arrivals',
        ],
        [
            'image' => 'hero/hero-tv.jpg',
            'eyebrow' => 'LED TVs & Entertainment',
            'title' => 'Cinematic Picture Quality At Home',
            'subtitle' => 'Explore QLED, OLED and smart TVs from Samsung, Sony, TCL and more.',
            'cta' => 'Shop TVs',
            'cta_href' => 'shop',
            'secondary' => 'View Categories',
            'secondary_href' => 'categories',
        ],
        [
            'image' => 'hero/hero-theater.jpg',
            'eyebrow' => 'Home Theater & Audio',
            'title' => 'Immersive Sound For Every Room',
            'subtitle' => 'Sound bars, AV receivers and complete home theater setups.',
            'cta' => 'Shop Audio',
            'cta_href' => 'shop',
            'secondary' => 'Contact Expert',
            'secondary_href' => 'contact',
        ],
    ],

    'promo_banners' => [
        [
            'title' => 'Electronics Deals',
            'subtitle' => 'Up to 30% off selected TVs & audio',
            'image' => 'banners/electronics-deals.jpg',
            'href' => 'shop',
        ],
        [
            'title' => 'Audio Collection',
            'subtitle' => 'Premium sound bars & speakers',
            'image' => 'banners/audio-collection.jpg',
            'href' => 'shop',
        ],
        [
            'title' => 'Appliances Offers',
            'subtitle' => 'Air purifiers & smart home',
            'image' => 'banners/appliances-offers.jpg',
            'href' => 'shop',
        ],
    ],

    'featured_collections' => [
        [
            'title' => 'Home Entertainment',
            'subtitle' => 'TVs, sound bars & theater systems',
            'image' => 'banners/home-entertainment.jpg',
            'href' => 'shop',
        ],
        [
            'title' => 'Smart Home',
            'subtitle' => 'Purifiers, gadgets & accessories',
            'image' => 'banners/smart-home.jpg',
            'href' => 'shop',
        ],
    ],

    'category_banners' => [
        ['title' => 'LED TVs', 'image' => 'images/categories/led-tvs.jpg', 'href' => 'shop'],
        ['title' => 'Sound Bars', 'image' => 'images/categories/sound-bars.jpg', 'href' => 'shop'],
        ['title' => 'Air Purifiers', 'image' => 'images/categories/air-purifiers.jpg', 'href' => 'shop'],
    ],

    'brands' => [
        ['id' => 'samsung', 'name' => 'Samsung'],
        ['id' => 'tcl', 'name' => 'TCL'],
        ['id' => 'sony', 'name' => 'Sony'],
        ['id' => 'panasonic', 'name' => 'Panasonic'],
        ['id' => 'sharp', 'name' => 'Sharp'],
        ['id' => 'pioneer', 'name' => 'Pioneer'],
        ['id' => 'denon', 'name' => 'Denon'],
    ],

    'ai_features' => [
        ['title' => 'Smart Recommendations', 'description' => 'Get product suggestions based on your room size and budget.'],
        ['title' => 'Compare Products', 'description' => 'Side-by-side specs for TVs, sound bars and appliances.'],
        ['title' => 'Expert Guidance', 'description' => 'Ask our team for help choosing the right setup.'],
    ],

    'reviews' => [
        ['name' => 'Ahmed K.', 'rating' => 5, 'text' => 'Excellent service and genuine products. My Samsung TV arrived perfectly packed.'],
        ['name' => 'Sara M.', 'rating' => 5, 'text' => 'Delivery was quick and packaging was secure. Sound bar setup was easy.'],
        ['name' => 'Hassan R.', 'rating' => 5, 'text' => 'Support team guided me to the right air purifier model for my home.'],
    ],

    'faqs' => [
        ['q' => 'How long is delivery?', 'a' => 'Usually 2–5 business days nationwide across Pakistan.'],
        ['q' => 'Are products original?', 'a' => 'Yes, all products are 100% genuine with official manufacturer warranty.'],
        ['q' => 'What payment methods do you accept?', 'a' => 'Cash on delivery, bank transfer and secure card payments.'],
        ['q' => 'Can I return a product?', 'a' => 'Unused items in original packaging can be returned within 7 days per our policy.'],
    ],

    'contact_cards' => [
        ['title' => 'Customer Support', 'value' => '+92 300 1234567', 'description' => 'Mon–Sat, 10am–8pm'],
        ['title' => 'Orders & Delivery', 'value' => 'orders@mybeststore.pk', 'description' => 'Track orders and shipping updates'],
        ['title' => 'Business Inquiries', 'value' => 'business@mybeststore.pk', 'description' => 'Wholesale and corporate sales'],
    ],

    'category_images' => [
        'led-tvs' => 'images/categories/led-tvs.jpg',
        'sound-bars' => 'images/categories/sound-bars.jpg',
        'air-purifiers' => 'images/categories/air-purifiers.jpg',
        'home-theater' => 'images/categories/home-theater.jpg',
        'audio-equipment' => 'images/categories/audio-equipment.jpg',
        'blu-ray-movies' => 'images/categories/blu-ray-movies.jpg',
        '4k-moives' => 'images/categories/4k-moives.jpg',
        'lp-records' => 'images/categories/lp-records.jpg',
        'lp-record' => 'images/categories/lp-records.jpg',
        'mobile-accessories' => 'images/categories/mobile-accessories.jpg',
        'tv-trolly-stand' => 'images/categories/tv-trolly-stand.jpg',
        'accessories' => 'images/categories/accessories.jpg',
    ],
];
