<?php

namespace Cms\Models;

class HeroSlide extends CmsModel
{
    protected $table = 'HeroSlides';

    protected $fillable = [
        'image', 'eyebrow', 'title', 'subtitle',
        'cta', 'cta_href', 'secondary', 'secondary_href',
    ];
}
