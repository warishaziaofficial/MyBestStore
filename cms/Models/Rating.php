<?php

namespace Cms\Models;

class Rating extends CmsModel
{
    protected $table = 'Ratings';

    protected $fillable = [
        'product_id',
        'customer_id',
        'reviewer_name',
        'rating',
        'status',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'customer_id' => 'integer',
        'rating' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saved(function (Rating $rating): void {
            \App\Support\ProductRatingAggregator::sync($rating->product_id);
        });

        static::deleted(function (Rating $rating): void {
            \App\Support\ProductRatingAggregator::sync($rating->product_id);
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
