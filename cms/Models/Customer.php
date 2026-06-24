<?php

namespace Cms\Models;

class Customer extends CmsModel
{
    protected $table = 'Customers';

    protected $fillable = ['name', 'email', 'phone', 'password', 'remember_token'];

    protected $hidden = ['password'];
}
