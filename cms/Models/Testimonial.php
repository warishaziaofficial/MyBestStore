<?php

namespace Cms\Models;

class Testimonial extends CmsModel
{
    protected $table = 'Testimonials';

    protected $fillable = [
        'name',
        'rating',
        'text',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];
}
