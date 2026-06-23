<?php

namespace Cms\Models;

class BlogCategory extends CmsModel
{
    protected $table = 'BlogCategories';

    protected $fillable = ['label', 'slug'];
}
