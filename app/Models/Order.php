<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'order_number',
        'order_barcode',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'city',
        'province',
        'country',
        'postal_code',
        'subtotal',
        'shipping_amount',
        'shipping_method',
        'shipping_zone',
        'total_weight',
        'discount_amount',
        'total_amount',
        'payment_method',
        'payment_status',
        'payment_reference',
        'payment_notes',
        'order_status',
        'shipping_status',
        'courier_company_id',
        'courier_name',
        'tracking_number',
        'dispatched_at',
        'dispatched_by',
        'dispatch_notes',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total_weight' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'dispatched_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function courierCompany(): BelongsTo
    {
        return $this->belongsTo(CourierCompany::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isDispatched(): bool
    {
        return in_array($this->shipping_status, ['dispatched', 'in_transit', 'delivered'], true)
            || in_array($this->order_status, ['dispatched', 'shipped', 'delivered'], true);
    }
}
