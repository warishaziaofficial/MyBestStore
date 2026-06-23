<?php

namespace Cms\Models;

class Category extends CmsModel
{
    protected $table = 'Categories';

    protected $fillable = ['name', 'slug', 'count', 'image', 'image_alt', 'description'];

    protected $casts = ['count' => 'integer'];
}
