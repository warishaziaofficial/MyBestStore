<?php

namespace Cms\Models;

class SocialSyncLog extends CmsModel
{
    protected $table = 'SocialSyncLogs';

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected $fillable = [
        'social_account_id',
        'platform',
        'trigger_type',
        'status',
        'orders_imported',
        'external_order_id',
        'order_id',
        'payload',
        'message',
        'created_at',
    ];

    protected $casts = [
        'orders_imported' => 'integer',
        'payload' => 'array',
        'created_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(SocialAccount::class, 'social_account_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
