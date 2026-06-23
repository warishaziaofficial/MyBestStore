<?php

namespace Cms\Models;

class Inquiry extends CmsModel
{
    protected $table = 'Inquiries';

    protected $fillable = ['name', 'email', 'phone', 'subject', 'message'];
}
