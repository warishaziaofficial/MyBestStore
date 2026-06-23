<?php

namespace Cms\Models;

class User extends CmsModel
{
    protected $table = 'Users';

    protected $fillable = ['username', 'email', 'password', 'role'];

    protected $hidden = ['password'];
}
