<?php

namespace Cms\Models;

class AdminNotification extends CmsModel
{
    protected $table = 'AdminNotifications';

    protected $fillable = [
        'type', 'title', 'body', 'link', 'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];
}
