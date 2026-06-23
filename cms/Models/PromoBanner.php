<?php

namespace Cms\Models;

class PromoBanner extends CmsModel
{
    protected $table = 'PromoBanners';

    protected $fillable = [
        'label',
        'title',
        'image',
        'href',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];
}
