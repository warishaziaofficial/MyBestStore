<?php

namespace Cms\Models;

use Illuminate\Database\Eloquent\Model;

abstract class CmsModel extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';
}
