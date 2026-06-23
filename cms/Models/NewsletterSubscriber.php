<?php

namespace Cms\Models;

class NewsletterSubscriber extends CmsModel
{
    protected $table = 'NewsletterSubscribers';

    protected $fillable = ['email'];
}
