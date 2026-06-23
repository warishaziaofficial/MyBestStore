<?php

namespace Cms\Support;

class ModuleMeta
{
    public static function for(string $entity): array
    {
        return self::map()[$entity] ?? [
            'icon' => '📄',
            'description' => 'Manage '.$entity.' records for your storefront.',
            'search_columns' => [],
        ];
    }

    private static function map(): array
    {
        return [
            'products' => ['icon' => '📦', 'description' => 'Your full product catalog — pricing, stock, images and merchandising.', 'search_columns' => ['name', 'slug', 'category', 'brand']],
            'categories' => ['icon' => '🗂️', 'description' => 'Store categories shown on the homepage and shop filters.', 'search_columns' => ['name', 'slug']],
            'brands' => ['icon' => '🏷️', 'description' => 'Brands used on products and the shop-by-brand section.', 'search_columns' => ['id', 'name']],
            'customers' => ['icon' => '👤', 'description' => 'Storefront shopper accounts (separate from CMS admins).', 'search_columns' => ['email']],
            'ratings' => ['icon' => '⭐', 'description' => 'Star ratings submitted on products — approve or reject.', 'search_columns' => ['reviewer_name']],
            'reviews' => ['icon' => '💬', 'description' => 'Written product reviews — moderation queue.', 'search_columns' => ['reviewer_name', 'title']],
            'testimonials' => ['icon' => '❝', 'description' => 'Homepage customer quotes — featured flag and sort order.', 'search_columns' => ['name']],
            'orders' => ['icon' => '🛒', 'description' => 'All store orders including website checkout and social imports.', 'search_columns' => ['order_number', 'customer_name', 'customer_email']],
            'order-items' => ['icon' => '📋', 'description' => 'Line items linked to each order.', 'search_columns' => ['product_name']],
            'blog-posts' => ['icon' => '📝', 'description' => 'Blog articles shown on /blog and the homepage.', 'search_columns' => ['title', 'slug', 'category']],
            'blog-categories' => ['icon' => '📂', 'description' => 'Categories for organizing blog posts.', 'search_columns' => ['label', 'slug']],
            'blog-tags' => ['icon' => '#', 'description' => 'Tags for blog sidebar and filtering.', 'search_columns' => ['tag']],
            'static-pages' => ['icon' => '📄', 'description' => 'About, Privacy, Terms, Warranty and other footer pages.', 'search_columns' => ['title', 'slug']],
            'product-images' => ['icon' => '🖼️', 'description' => 'Extra gallery images for product detail pages.', 'search_columns' => []],
            'refunds' => ['icon' => '↩', 'description' => 'Refund requests linked to orders.', 'search_columns' => ['reason']],
            'email-templates' => ['icon' => '✉', 'description' => 'Transactional email copy for orders, password reset and more.', 'search_columns' => ['slug', 'name', 'subject']],
            'inquiries' => ['icon' => '📩', 'description' => 'Contact form messages from the storefront.', 'search_columns' => ['name', 'email', 'subject']],
            'newsletter-subscribers' => ['icon' => '📧', 'description' => 'Newsletter sign-ups from the footer.', 'search_columns' => ['email']],
            'faqs' => ['icon' => '❓', 'description' => 'Homepage FAQ accordion content.', 'search_columns' => ['q']],
            'hero-slides' => ['icon' => '🎠', 'description' => 'Homepage hero slider slides.', 'search_columns' => ['title', 'eyebrow']],
            'users' => ['icon' => '🔐', 'description' => 'CMS admin, editor and viewer accounts.', 'search_columns' => ['username', 'email']],
            'media' => ['icon' => '📁', 'description' => 'Uploaded images for products, banners and content.', 'search_columns' => ['filename', 'path']],
            'promo-banners' => ['icon' => '🎯', 'description' => 'Special offers block on the homepage.', 'search_columns' => ['title', 'label']],
            'featured-collections' => ['icon' => '✨', 'description' => 'Curated collection cards on the homepage.', 'search_columns' => ['title', 'subtitle']],
            'contact-cards' => ['icon' => '📞', 'description' => 'Contact page cards — phone, email, address.', 'search_columns' => ['title']],
            'trust-items' => ['icon' => '🛡️', 'description' => 'Why shop with us trust bar on the homepage.', 'search_columns' => ['title']],
            'social-accounts' => ['icon' => '🔗', 'description' => 'Connected social selling accounts.', 'search_columns' => ['platform', 'account_name']],
            'social-sync-logs' => ['icon' => '📊', 'description' => 'Read-only log of social order sync runs.', 'search_columns' => ['platform', 'external_order_id']],
        ];
    }
}
