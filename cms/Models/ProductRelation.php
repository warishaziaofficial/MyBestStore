<?php

namespace Cms\Models;

class ProductRelation extends CmsModel
{
    protected $table = 'ProductRelations';

    protected $fillable = [
        'product_id',
        'related_product_id',
        'relation_type',
        'sort_order',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'related_product_id' => 'integer',
        'sort_order' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function relatedProduct()
    {
        return $this->belongsTo(Product::class, 'related_product_id');
    }
}
