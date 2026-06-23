<?php

namespace Cms\Models;

class Customer extends CmsModel
{
    protected $table = 'Customers';

    protected $fillable = ['email', 'password'];

    protected $hidden = ['password'];
}
