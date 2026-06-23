<?php

namespace Cms\Support;

use Cms\Models\Order;
use Cms\Models\Product;
use Illuminate\Support\Facades\Schema;

class AdminStats
{
    public static function dashboard(): array
    {
        if (! Schema::hasTable('Products') || ! Schema::hasTable('Orders')) {
            return [
                'total_products' => 0,
                'total_orders' => 0,
                'total_revenue' => 0,
                'pending_orders' => 0,
                'orders_by_status' => [],
                'orders_by_source' => [],
            ];
        }

        $ordersQuery = Order::query();

        return [
            'total_products' => Product::query()->count(),
            'total_orders' => (clone $ordersQuery)->count(),
            'website_orders' => (clone $ordersQuery)->where('source', 'website')->count(),
            'social_orders' => Schema::hasColumn('Orders', 'source')
                ? (clone $ordersQuery)->where('source', '!=', 'website')->count()
                : 0,
            'total_revenue' => (int) (clone $ordersQuery)
                ->where('status', '!=', 'cancelled')
                ->sum('total'),
            'pending_orders' => (clone $ordersQuery)->where('status', 'pending')->count(),
            'orders_by_status' => (clone $ordersQuery)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->all(),
            'orders_by_source' => Schema::hasColumn('Orders', 'source')
                ? (clone $ordersQuery)
                    ->selectRaw('source, COUNT(*) as count')
                    ->groupBy('source')
                    ->pluck('count', 'source')
                    ->all()
                : [],
        ];
    }
}
