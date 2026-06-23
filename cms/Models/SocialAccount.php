<?php

namespace Cms\Models;

class SocialAccount extends CmsModel
{
    protected $table = 'SocialAccounts';

    protected $fillable = [
        'platform',
        'account_name',
        'account_id',
        'status',
        'orders_synced_count',
        'last_sync_at',
        'webhook_secret',
        'notes',
    ];

    protected $casts = [
        'orders_synced_count' => 'integer',
        'last_sync_at' => 'datetime',
    ];

    public function syncLogs()
    {
        return $this->hasMany(SocialSyncLog::class, 'social_account_id');
    }
}
