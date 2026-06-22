<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRelation extends Model
{
    protected $fillable = [
        'product_slug',
        'related_product_slug',
        'relation_type',
        'sort_order',
    ];

    public const TYPE_UPSELL = 'upsell';

    public const TYPE_CROSS_SELL = 'cross_sell';

    public const TYPE_RELATED = 'related';
}
