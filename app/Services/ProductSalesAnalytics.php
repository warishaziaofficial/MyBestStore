<?php

namespace App\Services;

use App\Support\CmsIntegration;
use App\Support\StorefrontData;
use Cms\Models\OrderItem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductSalesAnalytics
{
    private const CACHE_SECONDS = 300;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function bestSellingProducts(int $limit = 12, int $days = 90): array
    {
        $ids = $this->topProductIdsByUnits($limit, $days);

        if ($ids === []) {
            return [];
        }

        return $this->mapIdsToProducts($ids);
    }

    /**
     * @return array<int, int> product_id => units sold
     */
    public function salesRankMap(int $days = 90): array
    {
        if (! $this->hasOrderData()) {
            return [];
        }

        return Cache::remember("sales.rank.{$days}", self::CACHE_SECONDS, function () use ($days) {
            $since = now()->subDays($days);

            return OrderItem::query()
                ->join('Orders', 'Orders.id', '=', 'OrderItems.order_id')
                ->where('Orders.created_at', '>=', $since)
                ->where('Orders.status', '!=', 'cancelled')
                ->whereNotNull('OrderItems.product_id')
                ->select('OrderItems.product_id', DB::raw('SUM(OrderItems.quantity) as units'))
                ->groupBy('OrderItems.product_id')
                ->pluck('units', 'product_id')
                ->map(fn ($units) => (int) $units)
                ->all();
        });
    }

    /**
     * @return list<int>
     */
    public function coPurchasedProductIds(int $productId, int $limit = 6, int $days = 180): array
    {
        if (! $this->hasOrderData() || $productId <= 0) {
            return [];
        }

        $cacheKey = "sales.co_purchase.{$productId}.{$limit}.{$days}";

        return Cache::remember($cacheKey, self::CACHE_SECONDS, function () use ($productId, $limit, $days) {
            $since = now()->subDays($days);

            return OrderItem::query()
                ->from('OrderItems as oi1')
                ->join('OrderItems as oi2', function ($join) {
                    $join->on('oi1.order_id', '=', 'oi2.order_id')
                        ->whereColumn('oi1.product_id', '!=', 'oi2.product_id');
                })
                ->join('Orders as o', 'o.id', '=', 'oi1.order_id')
                ->where('oi1.product_id', $productId)
                ->whereNotNull('oi2.product_id')
                ->where('o.created_at', '>=', $since)
                ->where('o.status', '!=', 'cancelled')
                ->select('oi2.product_id', DB::raw('SUM(oi2.quantity) as pair_units'))
                ->groupBy('oi2.product_id')
                ->orderByDesc('pair_units')
                ->limit($limit)
                ->pluck('oi2.product_id')
                ->map(fn ($id) => (int) $id)
                ->all();
        });
    }

    /**
     * @param  list<int>  $productIds
     * @return list<int>
     */
    public function coPurchasedForMany(array $productIds, int $limit = 6, int $days = 180): array
    {
        $productIds = array_values(array_unique(array_filter(array_map('intval', $productIds))));

        if ($productIds === []) {
            return [];
        }

        $ranked = [];

        foreach ($productIds as $productId) {
            foreach ($this->coPurchasedProductIds($productId, $limit * 2, $days) as $relatedId) {
                if (in_array($relatedId, $productIds, true)) {
                    continue;
                }

                $ranked[$relatedId] = ($ranked[$relatedId] ?? 0) + 1;
            }
        }

        arsort($ranked);

        return array_slice(array_keys($ranked), 0, $limit);
    }

    /**
     * @return list<int>
     */
    public function topProductIdsByUnits(int $limit, int $days = 90): array
    {
        if (! $this->hasOrderData()) {
            return [];
        }

        $rank = $this->salesRankMap($days);

        if ($rank === []) {
            return [];
        }

        arsort($rank);

        return array_slice(array_map('intval', array_keys($rank)), 0, $limit);
    }

    /**
     * @param  list<int>  $ids
     * @return array<int, array<string, mixed>>
     */
    public function mapIdsToProducts(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $catalog = collect(StorefrontData::allProducts())->keyBy('id');
        $products = [];

        foreach ($ids as $id) {
            $product = $catalog->get($id);

            if (! $product) {
                continue;
            }

            $products[] = StorefrontData::enrichProduct($product);
        }

        return $products;
    }

    /**
     * @param  array<int, array<string, mixed>>  $products
     */
    public function sortBySales(array $products, int $days = 90): array
    {
        $rank = $this->salesRankMap($days);

        usort($products, function (array $a, array $b) use ($rank) {
            $unitsA = $rank[(int) ($a['id'] ?? 0)] ?? 0;
            $unitsB = $rank[(int) ($b['id'] ?? 0)] ?? 0;

            return $unitsB <=> $unitsA
                ?: ((int) ($b['review_count'] ?? 0)) <=> ((int) ($a['review_count'] ?? 0))
                ?: ((float) ($b['rating'] ?? 0)) <=> ((float) ($a['rating'] ?? 0));
        });

        return $products;
    }

    private function hasOrderData(): bool
    {
        return CmsIntegration::usesCmsCatalog()
            && Schema::hasTable('OrderItems')
            && Schema::hasTable('Orders');
    }
}
