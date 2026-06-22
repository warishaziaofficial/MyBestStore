<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingZone extends Model
{
    protected $fillable = [
        'name',
        'country',
        'province',
        'city',
        'is_remote',
        'status',
    ];

    protected $casts = [
        'is_remote' => 'boolean',
    ];

    public function rates(): HasMany
    {
        return $this->hasMany(ShippingRate::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
