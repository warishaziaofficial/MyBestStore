<?php

namespace Cms\Models;

class OrderItem extends CmsModel
{
    protected $table = 'OrderItems';

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'line_total',
    ];

    protected $casts = [
        'order_id' => 'integer',
        'product_id' => 'integer',
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'line_total' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getProductSlugAttribute(): string
    {
        if ($this->relationLoaded('product') && $this->product) {
            return (string) $this->product->slug;
        }

        if ($this->product_id) {
            return (string) Product::query()->whereKey($this->product_id)->value('slug');
        }

        return '';
    }

    public function getProductImageAttribute(): ?string
    {
        if ($this->relationLoaded('product') && $this->product) {
            return $this->product->image;
        }

        if ($this->product_id) {
            return Product::query()->whereKey($this->product_id)->value('image');
        }

        return null;
    }
}
