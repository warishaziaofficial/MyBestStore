<?php

namespace Cms\Support;

use Cms\Models\BlogCategory;
use Cms\Models\BlogPost;
use Cms\Models\BlogTag;
use Cms\Models\Brand;
use Cms\Models\Category;
use Cms\Models\ContactCard;
use Cms\Models\Customer;
use Cms\Models\EmailTemplate;
use Cms\Models\Faq;
use Cms\Models\FeaturedCollection;
use Cms\Models\HeroSlide;
use Cms\Models\Inquiry;
use Cms\Models\Media;
use Cms\Models\NewsletterSubscriber;
use Cms\Models\Order;
use Cms\Models\OrderItem;
use Cms\Models\Product;
use Cms\Models\ProductImage;
use Cms\Models\PromoBanner;
use Cms\Models\Rating;
use Cms\Models\Refund;
use Cms\Models\Review;
use Cms\Models\SocialAccount;
use Cms\Models\SocialSyncLog;
use Cms\Models\StaticPage;
use Cms\Models\Testimonial;
use Cms\Models\TrustItem;
use Cms\Models\User;

class ResourceRegistry
{
    public static function all(): array
    {
        return [
            'products' => [
                'model' => Product::class,
                'title' => 'Products',
                'singular' => 'Product',
                'columns' => ['id', 'name', 'category', 'price', 'stock', 'featured', 'brand'],
                'fields' => [
                    'name' => ['type' => 'text', 'required' => true],
                    'slug' => ['type' => 'text', 'required' => true],
                    'price' => ['type' => 'number', 'required' => true],
                    'cost_price' => ['type' => 'number', 'required' => false],
                    'old_price' => ['type' => 'number', 'required' => false],
                    'image' => ['type' => 'image', 'required' => true],
                    'image_alt' => ['type' => 'text', 'required' => true],
                    'category' => ['type' => 'relation_select', 'source' => 'categories', 'required' => true],
                    'sub_category' => ['type' => 'text', 'required' => true],
                    'description' => ['type' => 'richtext', 'required' => false],
                    'rating' => ['type' => 'number', 'step' => '0.1', 'required' => false, 'edit' => false],
                    'review_count' => ['type' => 'number', 'required' => false, 'edit' => false],
                    'badge' => ['type' => 'select', 'options' => ['', 'SALE', 'NEW', 'FEATURED'], 'required' => false],
                    'featured' => ['type' => 'checkbox', 'required' => false],
                    'brand' => ['type' => 'relation_select', 'source' => 'brands', 'required' => true],
                    'stock' => ['type' => 'number', 'required' => true],
                ],
            ],
            'categories' => [
                'model' => Category::class,
                'title' => 'Categories',
                'singular' => 'Category',
                'columns' => ['id', 'name', 'slug', 'count'],
                'fields' => [
                    'name' => ['type' => 'text', 'required' => true],
                    'slug' => ['type' => 'text', 'required' => false, 'hint' => 'Leave blank — created automatically from the category name (used in shop URLs).'],
                    'count' => ['type' => 'number', 'required' => false, 'hidden' => true],
                    'image' => ['type' => 'image', 'required' => true, 'upload_only' => true, 'hint' => 'Shown on homepage category tiles. Upload here — no need to open Media Library separately.'],
                    'image_alt' => ['type' => 'text', 'required' => false, 'hint' => 'Optional. Leave blank to use the category name.'],
                    'description' => ['type' => 'textarea', 'required' => false, 'hint' => 'Optional short text on category cards.'],
                ],
            ],
            'brands' => [
                'model' => Brand::class,
                'title' => 'Brands',
                'singular' => 'Brand',
                'key' => 'id',
                'columns' => ['name', 'logo'],
                'fields' => [
                    'name' => ['type' => 'text', 'required' => true, 'hint' => 'Used on products and the shop-by-brand section.'],
                    'logo' => [
                        'type' => 'image',
                        'required' => false,
                        'upload_only' => true,
                        'hint' => 'Brand logo for the shop-by-brand section. Upload PNG, JPG, WebP, or SVG.',
                    ],
                    'sort_order' => ['type' => 'number', 'required' => false, 'hint' => 'Lower numbers appear first on homepage.'],
                ],
            ],
            'customers' => [
                'model' => Customer::class,
                'title' => 'Customers',
                'singular' => 'Customer',
                'columns' => ['id', 'email', 'created_at'],
                'fields' => [
                    'email' => ['type' => 'email', 'required' => true],
                    'password' => ['type' => 'password', 'required' => true, 'hash' => true],
                ],
            ],
            'ratings' => [
                'model' => Rating::class,
                'title' => 'Ratings',
                'singular' => 'Rating',
                'columns' => ['id', 'product_id', 'reviewer_name', 'rating', 'status'],
                'moderation' => true,
                'fields' => [
                    'product_id' => ['type' => 'relation_select', 'source' => 'products', 'required' => true],
                    'customer_id' => ['type' => 'number', 'required' => false],
                    'reviewer_name' => ['type' => 'text', 'required' => true],
                    'rating' => ['type' => 'number', 'required' => true],
                    'status' => ['type' => 'select', 'options' => ['pending', 'approved', 'rejected'], 'required' => true],
                ],
            ],
            'reviews' => [
                'model' => Review::class,
                'title' => 'Reviews',
                'singular' => 'Review',
                'columns' => ['id', 'product_id', 'reviewer_name', 'title', 'status'],
                'moderation' => true,
                'fields' => [
                    'product_id' => ['type' => 'relation_select', 'source' => 'products', 'required' => true],
                    'customer_id' => ['type' => 'number', 'required' => false],
                    'reviewer_name' => ['type' => 'text', 'required' => true],
                    'title' => ['type' => 'text', 'required' => true],
                    'text' => ['type' => 'textarea', 'required' => true],
                    'status' => ['type' => 'select', 'options' => ['pending', 'approved', 'rejected'], 'required' => true],
                ],
            ],
            'testimonials' => [
                'model' => Testimonial::class,
                'title' => 'Testimonials',
                'singular' => 'Testimonial',
                'columns' => ['id', 'name', 'rating', 'sort_order', 'is_featured'],
                'fields' => [
                    'name' => ['type' => 'text', 'required' => true],
                    'rating' => ['type' => 'number', 'required' => true],
                    'text' => ['type' => 'textarea', 'required' => true],
                    'is_featured' => ['type' => 'checkbox', 'required' => false],
                    'sort_order' => ['type' => 'number', 'required' => false],
                ],
            ],
            'orders' => [
                'model' => Order::class,
                'title' => 'Orders',
                'singular' => 'Order',
                'columns' => ['id', 'order_number', 'customer_name', 'status', 'payment_method', 'payment_status', 'total'],
                'fields' => [
                    'order_number' => ['type' => 'text', 'required' => true],
                    'customer_id' => ['type' => 'number', 'required' => false],
                    'customer_name' => ['type' => 'text', 'required' => true],
                    'customer_email' => ['type' => 'email', 'required' => true],
                    'customer_phone' => ['type' => 'text', 'required' => false],
                    'source' => ['type' => 'select', 'options' => ['website', 'facebook', 'instagram', 'tiktok', 'whatsapp', 'other'], 'required' => true],
                    'external_order_id' => ['type' => 'text', 'required' => false, 'edit' => false],
                    'external_account_id' => ['type' => 'text', 'required' => false, 'edit' => false],
                    'status' => ['type' => 'select', 'options' => ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'], 'required' => true],
                    'payment_method' => ['type' => 'select', 'options' => ['cod', 'jazzcash'], 'required' => true],
                    'payment_status' => ['type' => 'select', 'options' => ['pending', 'paid', 'failed', 'refunded'], 'required' => true],
                    'subtotal' => ['type' => 'number', 'required' => true],
                    'shipping' => ['type' => 'number', 'required' => true],
                    'total' => ['type' => 'number', 'required' => true],
                    'notes' => ['type' => 'textarea', 'required' => false],
                ],
            ],
            'order-items' => [
                'model' => OrderItem::class,
                'title' => 'Order Items',
                'singular' => 'Order Item',
                'columns' => ['id', 'order_id', 'product_name', 'quantity', 'line_total'],
                'fields' => [
                    'order_id' => ['type' => 'number', 'required' => true],
                    'product_id' => ['type' => 'number', 'required' => true],
                    'product_name' => ['type' => 'text', 'required' => true],
                    'quantity' => ['type' => 'number', 'required' => true],
                    'unit_price' => ['type' => 'number', 'required' => true],
                    'line_total' => ['type' => 'number', 'required' => true],
                ],
            ],
            'blog-posts' => [
                'model' => BlogPost::class,
                'title' => 'Blog Posts',
                'singular' => 'Blog Post',
                'columns' => ['id', 'title', 'slug', 'category', 'date'],
                'fields' => [
                    'title' => ['type' => 'text', 'required' => true],
                    'slug' => ['type' => 'text', 'required' => true],
                    'date' => ['type' => 'text', 'required' => true],
                    'category' => ['type' => 'relation_select', 'source' => 'blog-categories', 'required' => true],
                    'excerpt' => ['type' => 'textarea', 'required' => true],
                    'body' => ['type' => 'richtext', 'required' => false],
                    'image' => ['type' => 'image', 'required' => true],
                    'author' => ['type' => 'text', 'required' => true],
                ],
            ],
            'blog-categories' => [
                'model' => BlogCategory::class,
                'title' => 'Blog Categories',
                'singular' => 'Blog Category',
                'columns' => ['id', 'label', 'slug'],
                'fields' => [
                    'label' => ['type' => 'text', 'required' => true],
                    'slug' => ['type' => 'text', 'required' => true],
                ],
            ],
            'blog-tags' => [
                'model' => BlogTag::class,
                'title' => 'Blog Tags',
                'singular' => 'Blog Tag',
                'columns' => ['id', 'tag'],
                'fields' => [
                    'tag' => ['type' => 'text', 'required' => true],
                ],
            ],
            'static-pages' => [
                'model' => StaticPage::class,
                'title' => 'Static Pages',
                'singular' => 'Static Page',
                'columns' => ['id', 'slug', 'title', 'is_published'],
                'fields' => [
                    'slug' => ['type' => 'text', 'required' => true],
                    'title' => ['type' => 'text', 'required' => true],
                    'body' => ['type' => 'richtext', 'required' => true],
                    'is_published' => ['type' => 'checkbox', 'required' => false],
                ],
            ],
            'product-images' => [
                'model' => ProductImage::class,
                'title' => 'Product Gallery',
                'singular' => 'Product Image',
                'columns' => ['id', 'product_id', 'image', 'sort_order'],
                'fields' => [
                    'product_id' => ['type' => 'relation_select', 'source' => 'products', 'required' => true],
                    'image' => ['type' => 'image', 'required' => true],
                    'alt_text' => ['type' => 'text', 'required' => false],
                    'sort_order' => ['type' => 'number', 'required' => false],
                ],
            ],
            'refunds' => [
                'model' => Refund::class,
                'title' => 'Refunds',
                'singular' => 'Refund',
                'columns' => ['id', 'order_id', 'amount', 'status', 'reason'],
                'fields' => [
                    'order_id' => ['type' => 'number', 'required' => true],
                    'amount' => ['type' => 'number', 'required' => true],
                    'reason' => ['type' => 'text', 'required' => true],
                    'status' => ['type' => 'select', 'options' => ['pending', 'approved', 'rejected', 'completed'], 'required' => true],
                    'notes' => ['type' => 'textarea', 'required' => false],
                ],
            ],
            'email-templates' => [
                'model' => EmailTemplate::class,
                'title' => 'Email Templates',
                'singular' => 'Email Template',
                'columns' => ['id', 'slug', 'name', 'subject', 'is_active'],
                'fields' => [
                    'slug' => ['type' => 'text', 'required' => true],
                    'name' => ['type' => 'text', 'required' => true],
                    'subject' => ['type' => 'text', 'required' => true],
                    'body' => ['type' => 'textarea', 'required' => true],
                    'is_active' => ['type' => 'checkbox', 'required' => false],
                ],
            ],
            'inquiries' => [
                'model' => Inquiry::class,
                'title' => 'Inquiries',
                'singular' => 'Inquiry',
                'columns' => ['id', 'name', 'email', 'subject', 'created_at'],
                'fields' => [
                    'name' => ['type' => 'text', 'required' => true],
                    'email' => ['type' => 'email', 'required' => true],
                    'phone' => ['type' => 'text', 'required' => false],
                    'subject' => ['type' => 'text', 'required' => false],
                    'message' => ['type' => 'textarea', 'required' => true],
                ],
            ],
            'newsletter-subscribers' => [
                'model' => NewsletterSubscriber::class,
                'title' => 'Newsletter Subscribers',
                'singular' => 'Newsletter Subscriber',
                'columns' => ['id', 'email', 'created_at'],
                'fields' => [
                    'email' => ['type' => 'email', 'required' => true],
                ],
            ],
            'faqs' => [
                'model' => Faq::class,
                'title' => 'FAQs',
                'singular' => 'FAQ',
                'columns' => ['id', 'q'],
                'fields' => [
                    'q' => ['type' => 'text', 'required' => true],
                    'a' => ['type' => 'textarea', 'required' => true],
                ],
            ],
            'hero-slides' => [
                'model' => HeroSlide::class,
                'title' => 'Hero Slides',
                'singular' => 'Hero Slide',
                'columns' => ['id', 'title', 'eyebrow'],
                'fields' => [
                    'image' => ['type' => 'image', 'required' => true],
                    'eyebrow' => ['type' => 'text', 'required' => true],
                    'title' => ['type' => 'text', 'required' => true],
                    'subtitle' => ['type' => 'textarea', 'required' => true],
                    'cta' => ['type' => 'text', 'required' => true],
                    'cta_href' => ['type' => 'text', 'required' => true],
                    'secondary' => ['type' => 'text', 'required' => true],
                    'secondary_href' => ['type' => 'text', 'required' => true],
                ],
            ],
            'users' => [
                'model' => User::class,
                'title' => 'Users',
                'singular' => 'User',
                'admin_only' => true,
                'columns' => ['id', 'username', 'email', 'role'],
                'fields' => [
                    'username' => ['type' => 'text', 'required' => true],
                    'email' => ['type' => 'email', 'required' => true],
                    'password' => ['type' => 'password', 'required' => true, 'hash' => true],
                    'role' => ['type' => 'select', 'options' => ['admin', 'editor', 'viewer'], 'required' => true],
                ],
            ],
            'media' => [
                'model' => Media::class,
                'title' => 'Media Library',
                'singular' => 'Media File',
                'columns' => ['id', 'filename', 'path', 'mime_type', 'size_bytes'],
                'fields' => [
                    'upload' => ['type' => 'file', 'required' => true, 'virtual' => true, 'create' => true],
                    'filename' => ['type' => 'text', 'required' => false, 'edit' => false],
                    'path' => ['type' => 'text', 'required' => false, 'edit' => false],
                    'mime_type' => ['type' => 'text', 'required' => false, 'edit' => false],
                    'size_bytes' => ['type' => 'number', 'required' => false, 'edit' => false],
                    'alt_text' => ['type' => 'text', 'required' => false],
                ],
            ],
            'promo-banners' => [
                'model' => PromoBanner::class,
                'title' => 'Promo Banners',
                'singular' => 'Promo Banner',
                'columns' => ['id', 'label', 'title', 'sort_order', 'is_active'],
                'fields' => [
                    'label' => ['type' => 'text', 'required' => true],
                    'title' => ['type' => 'text', 'required' => true],
                    'image' => ['type' => 'image', 'required' => true],
                    'href' => ['type' => 'text', 'required' => true],
                    'sort_order' => ['type' => 'number', 'required' => false],
                    'is_active' => ['type' => 'checkbox', 'required' => false],
                ],
            ],
            'featured-collections' => [
                'model' => FeaturedCollection::class,
                'title' => 'Featured Collections',
                'singular' => 'Featured Collection',
                'columns' => ['id', 'title', 'subtitle', 'price', 'sort_order', 'is_active'],
                'fields' => [
                    'title' => ['type' => 'text', 'required' => true],
                    'subtitle' => ['type' => 'text', 'required' => true],
                    'image' => ['type' => 'image', 'required' => true],
                    'href' => ['type' => 'text', 'required' => true],
                    'product_slug' => ['type' => 'text', 'required' => false, 'label' => 'Product slug'],
                    'price' => ['type' => 'number', 'required' => false],
                    'sort_order' => ['type' => 'number', 'required' => false],
                    'is_active' => ['type' => 'checkbox', 'required' => false],
                ],
            ],
            'contact-cards' => [
                'model' => ContactCard::class,
                'title' => 'Contact Cards',
                'singular' => 'Contact Card',
                'columns' => ['id', 'title', 'value', 'sort_order', 'is_active'],
                'fields' => [
                    'title' => ['type' => 'text', 'required' => true],
                    'value' => ['type' => 'text', 'required' => true],
                    'description' => ['type' => 'text', 'required' => true],
                    'sort_order' => ['type' => 'number', 'required' => false],
                    'is_active' => ['type' => 'checkbox', 'required' => false],
                ],
            ],
            'trust-items' => [
                'model' => TrustItem::class,
                'title' => 'Trust Items',
                'singular' => 'Trust Item',
                'columns' => ['id', 'title', 'icon', 'sort_order', 'is_active'],
                'fields' => [
                    'title' => ['type' => 'text', 'required' => true],
                    'description' => ['type' => 'text', 'required' => true],
                    'icon' => ['type' => 'text', 'required' => true],
                    'sort_order' => ['type' => 'number', 'required' => false],
                    'is_active' => ['type' => 'checkbox', 'required' => false],
                ],
            ],
            'social-accounts' => [
                'model' => SocialAccount::class,
                'title' => 'Social Accounts',
                'singular' => 'Social Account',
                'columns' => ['id', 'platform', 'account_name', 'account_id', 'status', 'orders_synced_count', 'last_sync_at'],
                'fields' => [
                    'platform' => ['type' => 'select', 'options' => ['instagram', 'facebook', 'tiktok', 'whatsapp', 'other'], 'required' => true],
                    'account_name' => ['type' => 'text', 'required' => true],
                    'account_id' => ['type' => 'text', 'required' => true],
                    'status' => ['type' => 'select', 'options' => ['connected', 'disconnected', 'error'], 'required' => true],
                    'webhook_secret' => ['type' => 'text', 'required' => false],
                    'orders_synced_count' => ['type' => 'number', 'required' => false, 'edit' => false],
                    'last_sync_at' => ['type' => 'text', 'required' => false, 'edit' => false],
                    'notes' => ['type' => 'textarea', 'required' => false],
                ],
            ],
            'social-sync-logs' => [
                'model' => SocialSyncLog::class,
                'title' => 'Social Sync Logs',
                'singular' => 'Social Sync Log',
                'read_only' => true,
                'columns' => ['id', 'platform', 'trigger_type', 'status', 'orders_imported', 'external_order_id', 'order_id', 'created_at'],
                'fields' => [],
            ],
        ];
    }

    public static function get(string $entity): array
    {
        $all = self::all();

        if (! isset($all[$entity])) {
            abort(404);
        }

        return $all[$entity];
    }

    public static function keys(): string
    {
        return implode('|', array_keys(self::all()));
    }
}
