<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['category_id', 'brand_id', 'name', 'slug', 'description', 'price', 'compare_at_price', 'status'];
}
