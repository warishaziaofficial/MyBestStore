<?php

namespace Cms\Models;

class StaticPage extends CmsModel
{
    protected $table = 'StaticPages';

    protected $fillable = ['slug', 'title', 'body', 'is_published'];

    protected $casts = [
        'is_published' => 'boolean',
    ];
}
