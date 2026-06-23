<?php

namespace Cms\Models;

class Order extends CmsModel
{
    protected $table = 'Orders';

    protected $fillable = [
        'customer_id',
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'source',
        'external_order_id',
        'external_account_id',
        'source_metadata',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'shipping',
        'total',
        'notes',
        'courier_name',
        'tracking_number',
        'dispatched_at',
        'dispatch_meta',
    ];

    protected $casts = [
        'customer_id' => 'integer',
        'subtotal' => 'integer',
        'shipping' => 'integer',
        'total' => 'integer',
        'source_metadata' => 'array',
        'dispatch_meta' => 'array',
        'dispatched_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class, 'order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * @return array<string, mixed>
     */
    public function shippingMeta(): array
    {
        if (! filled($this->notes)) {
            return [];
        }

        $decoded = json_decode((string) $this->notes, true);

        return is_array($decoded['shipping'] ?? null) ? $decoded['shipping'] : [];
    }

    public function getOrderStatusAttribute(): string
    {
        return (string) ($this->attributes['status'] ?? 'pending');
    }

    public function getTotalAmountAttribute(): int
    {
        return (int) ($this->attributes['total'] ?? 0);
    }

    public function getShippingAmountAttribute(): int
    {
        return (int) ($this->attributes['shipping'] ?? 0);
    }

    public function getDiscountAmountAttribute(): int
    {
        return 0;
    }

    public function getShippingAddressAttribute(): ?string
    {
        return $this->shippingMeta()['address'] ?? null;
    }

    public function getCityAttribute(): ?string
    {
        return $this->shippingMeta()['city'] ?? null;
    }

    public function getProvinceAttribute(): ?string
    {
        return $this->shippingMeta()['province'] ?? null;
    }

    public function getCountryAttribute(): ?string
    {
        return $this->shippingMeta()['country'] ?? null;
    }

    public function getPostalCodeAttribute(): ?string
    {
        return $this->shippingMeta()['postal_code'] ?? null;
    }

    public function getShippingStatusAttribute(): ?string
    {
        return match ($this->order_status) {
            'shipped' => 'in_transit',
            'delivered' => 'delivered',
            'cancelled' => 'returned',
            default => 'pending',
        };
    }
}
