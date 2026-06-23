<?php

namespace Cms\Models;

class FeaturedCollection extends CmsModel
{
    protected $table = 'FeaturedCollections';

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'href',
        'product_slug',
        'price',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'price' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];
}
