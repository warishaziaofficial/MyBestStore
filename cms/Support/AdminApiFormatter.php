<?php

namespace Cms\Support;

use Cms\Models\Order;
use Cms\Models\OrderItem;
use Cms\Models\Product;

class AdminApiFormatter
{
    public static function product(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => (int) $product->price,
            'old_price' => $product->old_price ? (int) $product->old_price : null,
            'image' => $product->image,
            'image_alt' => $product->image_alt,
            'category' => $product->category,
            'sub_category' => $product->sub_category,
            'brand' => $product->brand,
            'stock' => (int) ($product->stock ?? 0),
            'featured' => (bool) $product->featured,
            'badge' => $product->badge,
            'rating' => round((float) $product->rating, 1),
            'review_count' => (int) $product->review_count,
            'created_at' => $product->created_at?->toIso8601String(),
            'updated_at' => $product->updated_at?->toIso8601String(),
            'description' => $product->description,
        ];
    }

    public static function order(Order $order, bool $withItems = false): array
    {
        $data = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'customer_id' => $order->customer_id,
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
            'customer_phone' => $order->customer_phone,
            'source' => $order->source ?? 'website',
            'external_order_id' => $order->external_order_id,
            'external_account_id' => $order->external_account_id,
            'source_metadata' => $order->source_metadata,
            'status' => $order->status,
            'payment_status' => $order->payment_status ?? 'pending',
            'subtotal' => (int) $order->subtotal,
            'shipping' => (int) $order->shipping,
            'total' => (int) $order->total,
            'notes' => $order->notes,
            'created_at' => $order->created_at?->toIso8601String(),
            'updated_at' => $order->updated_at?->toIso8601String(),
        ];

        if ($withItems) {
            $data['items'] = $order->items->map(fn (OrderItem $item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'quantity' => (int) $item->quantity,
                'unit_price' => (int) $item->unit_price,
                'line_total' => (int) $item->line_total,
            ])->values()->all();
        }

        return $data;
    }
}
