<?php

namespace Cms\Models;

class Faq extends CmsModel
{
    protected $table = 'Faqs';

    protected $fillable = ['q', 'a'];
}
