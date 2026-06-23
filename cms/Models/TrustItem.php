<?php

namespace Cms\Models;

class TrustItem extends CmsModel
{
    protected $table = 'TrustItems';

    protected $fillable = [
        'title',
        'description',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];
}
