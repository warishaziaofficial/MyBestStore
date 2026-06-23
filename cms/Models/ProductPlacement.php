<?php

namespace Cms\Models;

class ProductPlacement extends CmsModel
{
    protected $table = 'ProductPlacements';

    protected $fillable = ['product_id', 'placement', 'sort_order', 'is_active'];

    protected $casts = [
        'product_id' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
