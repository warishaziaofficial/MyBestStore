<?php

namespace Cms\Models;

class Product extends CmsModel
{
    protected $table = 'Products';

    protected $fillable = [
        'name', 'slug', 'price', 'cost_price', 'old_price', 'image', 'image_alt',
        'category', 'sub_category', 'description', 'rating', 'review_count', 'badge', 'featured', 'brand', 'stock',
    ];

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id')->orderBy('sort_order')->orderBy('id');
    }

    public function placements()
    {
        return $this->hasMany(ProductPlacement::class, 'product_id');
    }

    public function relations()
    {
        return $this->hasMany(ProductRelation::class, 'product_id');
    }

    protected $casts = [
        'featured' => 'boolean',
        'old_price' => 'integer',
        'cost_price' => 'integer',
        'price' => 'integer',
        'review_count' => 'integer',
        'rating' => 'float',
        'stock' => 'integer',
    ];
}
