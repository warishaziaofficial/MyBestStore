<?php

return [
    'allow_dev_fallback' => env('STOREFRONT_DEV_FALLBACK', false),
    'use_theme_defaults' => env('STOREFRONT_THEME_DEFAULTS', true),

    'navigation' => [
        ['label' => 'Home', 'href' => 'home', 'route' => true],
        ['label' => 'Shop', 'href' => 'shop', 'route' => true, 'mega' => true],
        ['label' => 'New Arrivals', 'href' => 'new-arrivals', 'route' => true],
        ['label' => 'Categories', 'href' => 'categories', 'route' => true],
        ['label' => 'Blog', 'href' => 'blog', 'route' => true],
        ['label' => 'Contact', 'href' => 'contact', 'route' => true],
    ],

    'premium_categories' => [
        [
            'title' => 'Premium TVs',
            'image' => 'images/categories/led-tvs.jpg',
            'href' => 'shop',
            'route' => true,
            'links' => [
                ['label' => 'Samsung Neo QLED 4K Smart TV', 'href' => 'shop', 'route' => true, 'image' => 'uploads/cms/mega-menu/2026/06/samsung-neo-qled-4k-smart-tv.jpg'],
                ['label' => 'LG OLED evo Smart TV', 'href' => 'shop', 'route' => true, 'image' => 'uploads/cms/mega-menu/2026/06/lg-oled-evo-smart-tv.jpg'],
                ['label' => 'Sony Bravia 4K Google TV', 'href' => 'shop', 'route' => true, 'image' => 'uploads/cms/mega-menu/2026/06/sony-bravia-4k-google-tv.jpg'],
            ],
        ],
        [
            'title' => 'Premium Audio',
            'image' => 'images/categories/sound-bars.jpg',
            'href' => 'shop',
            'route' => true,
            'links' => [
                ['label' => 'Samsung Dolby Atmos Soundbar', 'href' => 'shop', 'route' => true, 'image' => 'uploads/cms/mega-menu/2026/06/samsung-dolby-atmos-soundbar.jpg'],
                ['label' => 'Sony Wireless Noise Cancelling Headphones', 'href' => 'shop', 'route' => true, 'image' => 'uploads/cms/mega-menu/2026/06/sony-wireless-noise-cancelling-headphones.jpg'],
                ['label' => 'JBL PartyBox Speaker', 'href' => 'shop', 'route' => true, 'image' => 'uploads/cms/mega-menu/2026/06/jbl-partybox-speaker.jpg'],
                ['label' => 'Bose Bluetooth Speaker', 'href' => 'shop', 'route' => true, 'image' => 'uploads/cms/mega-menu/2026/06/bose-bluetooth-speaker.jpg'],
            ],
        ],
        [
            'title' => 'Smart Home',
            'image' => 'images/categories/air-purifiers.jpg',
            'href' => 'shop',
            'route' => true,
            'links' => [
                ['label' => 'Smart Air Purifier', 'href' => 'shop', 'route' => true, 'image' => 'uploads/cms/mega-menu/2026/06/smart-air-purifier.jpg'],
                ['label' => 'Robot Vacuum Cleaner', 'href' => 'shop', 'route' => true, 'image' => 'uploads/cms/mega-menu/2026/06/robot-vacuum-cleaner.jpg'],
                ['label' => 'Smart Door Lock', 'href' => 'shop', 'route' => true, 'image' => 'uploads/cms/mega-menu/2026/06/smart-door-lock.jpg'],
                ['label' => 'Smart Security Camera', 'href' => 'shop', 'route' => true, 'image' => 'uploads/cms/mega-menu/2026/06/smart-security-camera.jpg'],
            ],
        ],
        [
            'title' => 'Gaming & Tech',
            'image' => 'assets/images/feature-carousel/computer.jpg',
            'href' => 'shop',
            'route' => true,
            'links' => [
                ['label' => 'Gaming Headset', 'href' => 'shop', 'route' => true, 'image' => 'uploads/cms/mega-menu/2026/06/gaming-headset.jpg'],
                ['label' => 'Gaming Monitor', 'href' => 'shop', 'route' => true, 'image' => 'uploads/cms/mega-menu/2026/06/gaming-monitor.jpg'],
                ['label' => 'Wireless Gaming Controller', 'href' => 'shop', 'route' => true, 'image' => 'uploads/cms/mega-menu/2026/06/wireless-gaming-controller.jpg'],
                ['label' => 'Mechanical Keyboard', 'href' => 'shop', 'route' => true, 'image' => 'uploads/cms/mega-menu/2026/06/mechanical-keyboard.jpg'],
            ],
        ],
        [
            'title' => 'Accessories',
            'image' => 'images/categories/accessories.jpg',
            'href' => 'shop',
            'route' => true,
            'links' => [
                ['label' => 'Type-C Fast Charger', 'href' => 'shop', 'route' => true, 'image' => 'uploads/cms/mega-menu/2026/06/type-c-fast-charger.jpg'],
                ['label' => 'Wireless Charging Pad', 'href' => 'shop', 'route' => true, 'image' => 'uploads/cms/mega-menu/2026/06/wireless-charging-pad.jpg'],
                ['label' => 'Power Bank', 'href' => 'shop', 'route' => true, 'image' => 'uploads/cms/mega-menu/2026/06/power-bank.jpg'],
                ['label' => 'Smartwatch', 'href' => 'shop', 'route' => true, 'image' => 'uploads/cms/mega-menu/2026/06/smartwatch.jpg'],
            ],
        ],
    ],

    'mega_menu' => [],

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
            'title' => 'Home Entertainment Setup',
            'image' => 'banners/audio-entertainment.jpg',
            'category' => 'home-theater',
        ],
        [
            'title' => 'Smart Home Essentials',
            'image' => 'banners/smart-home.jpg',
            'category' => 'air-purifiers',
        ],
        [
            'title' => 'Premium Audio Collection',
            'image' => 'assets/images/offers/offer-headphones.jpg',
            'category' => 'sound-bars',
        ],
        [
            'title' => 'Work & Gaming Accessories',
            'image' => 'assets/images/feature-carousel/accessories.jpg',
            'category' => 'accessories',
        ],
        [
            'title' => 'Samsung Neo QLED TV',
            'image' => 'uploads/cms/mega-menu/2026/06/samsung-neo-qled-4k-smart-tv.jpg',
            'slug' => 'samsung-neo-qled-4k-smart-tv',
            'price' => 289999,
        ],
        [
            'title' => 'Sony Wireless Headphones',
            'image' => 'uploads/cms/mega-menu/2026/06/sony-wireless-noise-cancelling-headphones.jpg',
            'slug' => 'sony-wireless-noise-cancelling-headphones',
            'price' => 45999,
        ],
    ],

    'category_banners' => [
        ['title' => 'LED TVs', 'image' => 'images/categories/led-tvs.jpg', 'href' => 'shop', 'slug' => 'led-tvs', 'subtitle' => '4K, QLED & Smart TVs for every room'],
        ['title' => 'Sound Bars', 'image' => 'images/categories/sound-bars.jpg', 'href' => 'shop', 'slug' => 'sound-bars', 'subtitle' => 'Premium audio for home entertainment'],
        ['title' => 'Air Purifiers', 'image' => 'images/categories/air-purifiers.jpg', 'href' => 'shop', 'slug' => 'air-purifiers', 'subtitle' => 'Cleaner air for healthier living'],
    ],

    'brands' => [
        ['id' => 'samsung', 'name' => 'Samsung', 'logo' => 'assets/images/brands/samsung.svg'],
        ['id' => 'sony', 'name' => 'Sony', 'logo' => 'assets/images/brands/sony.svg'],
        ['id' => 'panasonic', 'name' => 'Panasonic', 'logo' => 'assets/images/brands/panasonic.svg'],
        ['id' => 'tcl', 'name' => 'TCL', 'logo' => 'assets/images/brands/tcl.svg'],
        ['id' => 'denon', 'name' => 'Denon', 'logo' => 'assets/images/brands/denon.svg'],
        ['id' => 'pioneer', 'name' => 'Pioneer', 'logo' => 'assets/images/brands/pioneer.svg'],
    ],

    'ai_features' => [
        ['title' => 'Smart Recommendations', 'description' => 'Get product suggestions based on your room size and budget.'],
        ['title' => 'Compare Products', 'description' => 'Side-by-side specs for TVs, sound bars and appliances.'],
        ['title' => 'Expert Guidance', 'description' => 'Ask our team for help choosing the right setup.'],
    ],

    'reviews' => [
        ['name' => 'Ahmed K.', 'rating' => 5, 'text' => 'Excellent service and genuine products. My Samsung TV arrived perfectly packed and on time.', 'purchase' => 'Samsung LED TV', 'avatar' => 'assets/images/testimonials/ahmed-k.jpg'],
        ['name' => 'Sara M.', 'rating' => 5, 'text' => 'Delivery was quick and the packaging was secure. The sound bar setup was very easy.', 'purchase' => 'Sound Bar', 'avatar' => 'assets/images/testimonials/sara-m.jpg'],
        ['name' => 'Hassan R.', 'rating' => 5, 'text' => 'Support team helped me choose the right air purifier for my home. Very satisfied.', 'purchase' => 'Air Purifier', 'avatar' => 'assets/images/testimonials/hassan-r.jpg'],
        ['name' => 'Zain A.', 'rating' => 5, 'text' => 'Prices were fair and checkout was smooth. I will definitely order again.', 'purchase' => 'Home Electronics', 'avatar' => 'assets/images/testimonials/zain-a.jpg'],
    ],

    'faqs' => [
        ['q' => 'Which products do you sell?', 'a' => 'We sell electronics, audio products, home appliances, accessories, books and media products.'],
        ['q' => 'How can I follow my order?', 'a' => 'You can track your order from the Track Order option or by contacting our support team.'],
        ['q' => 'How can I choose the right product?', 'a' => 'You can compare product details, check ratings, or use our shopping help section for recommendations.'],
        ['q' => 'Do you provide payment options?', 'a' => 'Yes, we support Cash on Delivery, Bank Transfer, JazzCash, EasyPaisa and card payment options.'],
        ['q' => 'Can I return or exchange a product?', 'a' => 'Yes, eligible products can be returned or exchanged according to our return policy.'],
        ['q' => 'How to redeem a discount code?', 'a' => 'Enter your discount code on the cart or checkout page and click Apply.'],
    ],

    'contact_cards' => [
        ['title' => 'Customer Support', 'value' => '+92 300 1234567', 'description' => 'Mon–Sat, 10am–8pm'],
        ['title' => 'Orders & Delivery', 'value' => 'orders@mybeststore.pk', 'description' => 'Track orders and shipping updates'],
        ['title' => 'Business Inquiries', 'value' => 'business@mybeststore.pk', 'description' => 'Wholesale and corporate sales'],
    ],

    /*
    | Contact page map — replace embed_url via CMS when ready.
    | Get embed URL from Google Maps → Share → Embed a map.
    */
    'contact_map' => [
        'title' => 'Find Us on the Map',
        'subtitle' => 'Plan your visit or get directions to our store',
        'address' => 'Karachi, Sindh, Pakistan',
        'embed_url' => 'https://maps.google.com/maps?q=Karachi,+Sindh,+Pakistan&hl=en&z=13&output=embed',
        'maps_link' => 'https://www.google.com/maps/search/?api=1&query=Karachi,+Sindh,+Pakistan',
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
