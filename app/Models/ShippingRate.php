<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingRate extends Model
{
    protected $fillable = [
        'shipping_zone_id',
        'method_name',
        'base_rate',
        'min_weight',
        'max_weight',
        'extra_rate',
        'free_shipping_min_amount',
        'status',
    ];

    protected $casts = [
        'base_rate' => 'decimal:2',
        'min_weight' => 'decimal:2',
        'max_weight' => 'decimal:2',
        'extra_rate' => 'decimal:2',
        'free_shipping_min_amount' => 'decimal:2',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
