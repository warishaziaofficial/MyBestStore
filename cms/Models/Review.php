<?php

namespace Cms\Models;

class Review extends CmsModel
{
    protected $table = 'Reviews';

    protected $fillable = [
        'product_id',
        'customer_id',
        'reviewer_name',
        'title',
        'text',
        'status',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'customer_id' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
