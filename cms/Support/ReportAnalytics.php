<?php

namespace Cms\Support;

use Cms\Models\Order;
use Cms\Models\OrderItem;
use Cms\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportAnalytics
{
    public static function lowStock(int $threshold = 5, int $limit = 20): Collection
    {
        if (! Schema::hasTable('Products')) {
            return collect();
        }

        return Product::query()
            ->where('stock', '>', 0)
            ->where('stock', '<=', $threshold)
            ->orderBy('stock')
            ->limit($limit)
            ->get(['id', 'name', 'stock', 'price']);
    }

    public static function outOfStock(int $limit = 20): Collection
    {
        if (! Schema::hasTable('Products')) {
            return collect();
        }

        return Product::query()
            ->where('stock', '<=', 0)
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'name', 'stock', 'price']);
    }

    public static function fastMoving(int $days = 30, int $limit = 10): array
    {
        if (! Schema::hasTable('OrderItems') || ! Schema::hasTable('Orders')) {
            return [];
        }

        $since = now()->subDays($days);

        return OrderItem::query()
            ->join('Orders', 'Orders.id', '=', 'OrderItems.order_id')
            ->where('Orders.created_at', '>=', $since)
            ->where('Orders.status', '!=', 'cancelled')
            ->select(
                'OrderItems.product_id',
                'OrderItems.product_name',
                DB::raw('SUM(OrderItems.quantity) as units'),
                DB::raw('SUM(OrderItems.line_total) as revenue')
            )
            ->groupBy('OrderItems.product_id', 'OrderItems.product_name')
            ->orderByDesc('units')
            ->limit($limit)
            ->get()
            ->all();
    }

    public static function slowMoving(int $days = 90, int $limit = 10): array
    {
        if (! Schema::hasTable('Products') || ! Schema::hasTable('OrderItems') || ! Schema::hasTable('Orders')) {
            return [];
        }

        $since = now()->subDays($days);

        $soldProductIds = OrderItem::query()
            ->join('Orders', 'Orders.id', '=', 'OrderItems.order_id')
            ->where('Orders.created_at', '>=', $since)
            ->where('Orders.status', '!=', 'cancelled')
            ->pluck('OrderItems.product_id')
            ->filter()
            ->unique()
            ->all();

        return Product::query()
            ->where('stock', '>', 0)
            ->when($soldProductIds !== [], fn ($query) => $query->whereNotIn('id', $soldProductIds))
            ->orderByDesc('stock')
            ->limit($limit)
            ->get(['id', 'name', 'stock', 'price'])
            ->map(fn (Product $product) => [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'stock' => (int) $product->stock,
                'units_sold' => 0,
            ])
            ->all();
    }

    public static function demandForecast(): array
    {
        if (! Schema::hasTable('Products') || ! Schema::hasTable('OrderItems') || ! Schema::hasTable('Orders')) {
            return [];
        }

        $months = 3;
        $since = now()->subMonths($months)->startOfMonth();

        $monthlySales = OrderItem::query()
            ->join('Orders', 'Orders.id', '=', 'OrderItems.order_id')
            ->where('Orders.created_at', '>=', $since)
            ->where('Orders.status', '!=', 'cancelled')
            ->whereNotNull('OrderItems.product_id')
            ->select(
                'OrderItems.product_id',
                DB::raw("DATE_FORMAT(Orders.created_at, '%Y-%m') as month"),
                DB::raw('SUM(OrderItems.quantity) as units')
            )
            ->groupBy('OrderItems.product_id', 'month')
            ->get()
            ->groupBy('product_id');

        $products = Product::query()->orderBy('name')->get(['id', 'name', 'stock']);

        return $products->map(function (Product $product) use ($monthlySales, $months) {
            $rows = $monthlySales->get($product->id, collect());
            $totalUnits = (int) $rows->sum('units');
            $activeMonths = max(1, $rows->count());
            $avgMonthly = (int) round($totalUnits / $activeMonths);
            $forecast = (int) max(0, round($avgMonthly * 1.05));

            return [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'current_stock' => (int) $product->stock,
                'avg_monthly_units' => $avgMonthly,
                'forecast_next_month' => $forecast,
            ];
        })
            ->filter(fn (array $row) => $row['forecast_next_month'] > 0)
            ->sortByDesc('forecast_next_month')
            ->take(15)
            ->values()
            ->all();
    }

    public static function purchaseRecommendations(): array
    {
        return collect(self::demandForecast())
            ->map(function (array $row) {
                $gap = $row['forecast_next_month'] - $row['current_stock'];
                $recommended = $gap > 0 ? $gap + 2 : 0;

                return array_merge($row, [
                    'recommended_qty' => $recommended,
                    'priority' => $recommended >= 10 ? 'high' : ($recommended >= 3 ? 'medium' : 'low'),
                ]);
            })
            ->filter(fn (array $row) => $row['recommended_qty'] > 0)
            ->sortByDesc('recommended_qty')
            ->take(15)
            ->values()
            ->all();
    }

    public static function highMargin(int $limit = 10): array
    {
        if (! Schema::hasTable('Products') || ! Schema::hasColumn('Products', 'cost_price')) {
            return [];
        }

        return Product::query()
            ->whereNotNull('cost_price')
            ->where('cost_price', '>', 0)
            ->where('price', '>', 0)
            ->get(['id', 'name', 'price', 'cost_price'])
            ->map(function (Product $product) {
                $margin = (int) $product->price - (int) $product->cost_price;
                $marginPct = round(($margin / max(1, (int) $product->price)) * 100, 1);

                return [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => (int) $product->price,
                    'cost_price' => (int) $product->cost_price,
                    'margin_amount' => $margin,
                    'margin_percent' => $marginPct,
                ];
            })
            ->sortByDesc('margin_percent')
            ->take($limit)
            ->values()
            ->all();
    }

    public static function trending(int $limit = 10): array
    {
        if (! Schema::hasTable('OrderItems') || ! Schema::hasTable('Orders')) {
            return [];
        }

        $recentStart = now()->subDays(7);
        $priorStart = now()->subDays(14);

        $recent = self::salesBetween($recentStart, now());
        $prior = self::salesBetween($priorStart, $recentStart->copy()->subSecond());

        $productIds = $recent->keys()->merge($prior->keys())->unique();

        return $productIds->map(function ($productId) use ($recent, $prior) {
            $recentUnits = (int) ($recent[$productId]['units'] ?? 0);
            $priorUnits = (int) ($prior[$productId]['units'] ?? 0);
            $growth = $priorUnits > 0
                ? round((($recentUnits - $priorUnits) / $priorUnits) * 100, 1)
                : ($recentUnits > 0 ? 100.0 : 0.0);

            return [
                'product_id' => $productId,
                'product_name' => $recent[$productId]['product_name'] ?? $prior[$productId]['product_name'] ?? 'Product #'.$productId,
                'recent_units' => $recentUnits,
                'prior_units' => $priorUnits,
                'growth_percent' => $growth,
            ];
        })
            ->filter(fn (array $row) => $row['recent_units'] > 0)
            ->sortByDesc('growth_percent')
            ->take($limit)
            ->values()
            ->all();
    }

    private static function salesBetween($start, $end): Collection
    {
        return OrderItem::query()
            ->join('Orders', 'Orders.id', '=', 'OrderItems.order_id')
            ->whereBetween('Orders.created_at', [$start, $end])
            ->where('Orders.status', '!=', 'cancelled')
            ->select(
                'OrderItems.product_id',
                'OrderItems.product_name',
                DB::raw('SUM(OrderItems.quantity) as units')
            )
            ->groupBy('OrderItems.product_id', 'OrderItems.product_name')
            ->get()
            ->keyBy('product_id')
            ->map(fn ($row) => [
                'product_name' => $row->product_name,
                'units' => (int) $row->units,
            ]);
    }
}
