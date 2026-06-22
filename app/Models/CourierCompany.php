<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourierCompany extends Model
{
    protected $fillable = [
        'name',
        'contact_number',
        'tracking_url',
        'status',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function trackingLink(?string $trackingNumber): ?string
    {
        if (! filled($trackingNumber) || ! filled($this->tracking_url)) {
            return null;
        }

        return str_replace('{tracking}', urlencode($trackingNumber), $this->tracking_url);
    }
}
