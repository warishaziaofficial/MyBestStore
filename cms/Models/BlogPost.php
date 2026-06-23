<?php

namespace Cms\Models;

class BlogPost extends CmsModel
{
    protected $table = 'BlogPosts';

    protected $fillable = ['title', 'slug', 'date', 'category', 'excerpt', 'body', 'image', 'author'];
}
