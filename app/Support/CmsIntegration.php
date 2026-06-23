<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

class CmsIntegration
{
    public static function usesCmsCatalog(): bool
    {
        try {
            return Schema::hasTable('Products');
        } catch (\Throwable) {
            return false;
        }
    }

    public static function usesCmsOrders(): bool
    {
        try {
            return Schema::hasTable('Orders') && Schema::hasTable('OrderItems');
        } catch (\Throwable) {
            return false;
        }
    }

    public static function preferCmsOrders(): bool
    {
        if (! self::usesCmsOrders()) {
            return false;
        }

        // Storefront + CMS database (Products, Orders) — checkout writes to CMS tables.
        if (self::usesCmsCatalog()) {
            return true;
        }

        try {
            if (Schema::hasTable('Customers') && ! Schema::hasColumn('Customers', 'name')) {
                return true;
            }

            return ! Schema::hasTable('orders');
        } catch (\Throwable) {
            return true;
        }
    }

    public static function cmsPaymentMethod(string $method): string
    {
        return match ($method) {
            'cash_on_delivery' => 'cod',
            default => $method,
        };
    }
}
