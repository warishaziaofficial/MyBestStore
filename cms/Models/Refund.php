<?php

namespace Cms\Models;

class Refund extends CmsModel
{
    protected $table = 'Refunds';

    protected $fillable = ['order_id', 'amount', 'reason', 'status', 'notes'];

    protected $casts = [
        'order_id' => 'integer',
        'amount' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
