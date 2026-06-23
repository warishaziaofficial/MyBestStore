<?php

namespace Cms\Support;

use Cms\Models\ProductRelation;
use Illuminate\Support\Facades\Schema;

class ProductRelationSync
{
    public const TYPES = [
        'upsell' => 'Upsell Products',
        'cross_sell' => 'Cross-sell Products',
        'related' => 'Related Products',
        'frequently_bought_together' => 'Frequently Bought Together',
    ];

    public static function groupedForProduct(int $productId): array
    {
        if (! Schema::hasTable('ProductRelations')) {
            return array_fill_keys(array_keys(self::TYPES), []);
        }

        $grouped = array_fill_keys(array_keys(self::TYPES), []);

        ProductRelation::query()
            ->where('product_id', $productId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->each(function (ProductRelation $relation) use (&$grouped) {
                $grouped[$relation->relation_type][] = (int) $relation->related_product_id;
            });

        return $grouped;
    }

    public static function sync(int $productId, array $relations): void
    {
        if (! Schema::hasTable('ProductRelations')) {
            return;
        }

        ProductRelation::query()->where('product_id', $productId)->delete();

        foreach (self::TYPES as $type => $label) {
            $ids = collect($relations[$type] ?? [])
                ->map(fn ($id) => (int) $id)
                ->filter(fn (int $id) => $id > 0 && $id !== $productId)
                ->unique()
                ->values();

            foreach ($ids as $index => $relatedId) {
                ProductRelation::create([
                    'product_id' => $productId,
                    'related_product_id' => $relatedId,
                    'relation_type' => $type,
                    'sort_order' => $index,
                ]);
            }
        }
    }
}
