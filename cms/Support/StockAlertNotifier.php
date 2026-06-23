<?php

namespace Cms\Support;

use App\Support\EmailTemplateMailer;
use Cms\Models\Product;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class StockAlertNotifier
{
    public static function counts(): array
    {
        return [
            'low_stock' => ReportAnalytics::lowStock()->count(),
            'out_of_stock' => ReportAnalytics::outOfStock()->count(),
        ];
    }

    public static function hasAlerts(): bool
    {
        $counts = self::counts();

        return ($counts['low_stock'] + $counts['out_of_stock']) > 0;
    }

    /**
     * Send admin email only when a product crosses from in-stock to out-of-stock.
     * No daily digests — triggered on checkout, social orders, or CMS stock edits.
     */
    public static function afterStockChange(int $productId, int $previousStock): void
    {
        if ($previousStock <= 0 || ! Schema::hasTable('Products')) {
            return;
        }

        $product = Product::query()->find($productId);

        if (! $product || (int) $product->stock > 0) {
            return;
        }

        self::sendOutOfStockEmail($product);

        AdminNotifier::push(
            'out_of_stock',
            'Out of stock: '.$product->name,
            'Stock reached zero. Restock or hide this product on the storefront.',
            url('/cms/manage/products/'.$product->id.'/edit'),
            true
        );
    }

    /** @return list<string> */
    public static function adminRecipients(): array
    {
        return EmailTemplateMailer::adminRecipients();
    }

    private static function sendOutOfStockEmail(Product $product): bool
    {
        $recipients = self::adminRecipients();

        if ($recipients === []) {
            return false;
        }

        $subject = 'Out of stock: '.$product->name;
        $body = implode("\n", [
            'MyBestStore — Out of Stock Alert',
            '================================',
            '',
            'A product has just gone out of stock:',
            '',
            'Product: '.$product->name,
            'Product ID: '.$product->id,
            'Slug: '.$product->slug,
            'Current stock: 0',
            '',
            'Edit product: '.url('/cms/manage/products/'.$product->id.'/edit'),
            'Inventory report: '.url('/cms/reports').'#inventory-alerts',
            '',
            'You receive this email only when stock reaches zero (not on a daily schedule).',
        ]);

        foreach ($recipients as $email) {
            Mail::raw($body, function ($message) use ($email, $subject): void {
                $message->to($email)->subject($subject);
            });
        }

        return true;
    }
}
