<?php

namespace Cms\Models;

class ProductImage extends CmsModel
{
    protected $table = 'ProductImages';

    protected $fillable = ['product_id', 'image', 'alt_text', 'sort_order'];

    protected $casts = [
        'product_id' => 'integer',
        'sort_order' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
