<?php

namespace Cms\Models;

class EmailTemplate extends CmsModel
{
    protected $table = 'EmailTemplates';

    protected $fillable = ['slug', 'name', 'subject', 'body', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
