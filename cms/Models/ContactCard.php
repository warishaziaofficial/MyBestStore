<?php

namespace Cms\Models;

class ContactCard extends CmsModel
{
    protected $table = 'ContactCards';

    protected $fillable = [
        'title',
        'value',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];
}
