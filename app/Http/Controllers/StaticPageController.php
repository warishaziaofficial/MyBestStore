<?php

namespace App\Http\Controllers;

use App\Support\StorefrontData;
use Illuminate\Contracts\View\View;

class StaticPageController extends Controller
{
    /**
     * @var array<string, array{title: string, description?: string}>
     */
    private const PAGES = [
        'terms-of-service' => [
            'title' => 'Terms Of Service',
            'description' => 'Please read these terms carefully before using MyBestStore.pk.',
        ],
        'privacy-policy' => [
            'title' => 'Privacy Policy',
            'description' => 'How we collect, use, and protect your personal information.',
        ],
        'return-policy' => [
            'title' => 'Return Policy',
            'description' => 'Eligible returns, timeframes, and how to start a return request.',
        ],
        'warranty-policy' => [
            'title' => 'Warranty Policy',
            'description' => 'Official manufacturer warranty coverage and claim guidance.',
        ],
        'faq' => [
            'title' => 'FAQ',
            'description' => 'Answers to common questions about shopping at MyBestStore.',
        ],
        'about-us' => [
            'title' => 'About Us',
            'description' => 'Pakistan\'s trusted destination for premium electronics and home entertainment.',
        ],
        'our-story' => [
            'title' => 'Our Story',
            'description' => 'How MyBestStore grew into a nationwide electronics retailer.',
        ],
        'product-guides' => [
            'title' => 'Product Guides',
            'description' => 'Expert buying advice for TVs, soundbars, air purifiers, and accessories.',
        ],
    ];

    public static function pages(): array
    {
        return self::PAGES;
    }

    public function show(string $page): View
    {
        $meta = self::PAGES[$page] ?? null;

        abort_unless($meta, 404);

        if ($page === 'about-us') {
            $posts = StorefrontData::blogPosts();

            return view('pages.about-us', array_merge($meta, [
                'guidePosts' => [
                    $posts[0],
                    array_merge($posts[1], ['title' => 'Best Soundbars for Premium Home Entertainment']),
                    $posts[2],
                ],
                'testimonials' => config('storefront.reviews', []),
            ]));
        }

        return view("pages.{$page}", $meta);
    }
}
