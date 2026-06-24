<?php

namespace Cms\Models;

class ChatSettings extends CmsModel
{
    protected $table = 'ChatSettings';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = [
        'is_enabled',
        'base_url',
        'website_token',
        'chatwoot_admin_url',
        'crm_url',
        'dify_url',
        'chatdify_url',
        'launcher_title',
        'welcome_title',
        'welcome_description',
        'widget_position',
        'fallback_enabled',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'fallback_enabled' => 'boolean',
        ];
    }

    public static function emptyDefaults(): array
    {
        return [
            'is_enabled' => (bool) config('chatwoot.enabled', false),
            'base_url' => (string) config('chatwoot.base_url', 'http://localhost:3000'),
            'website_token' => (string) config('chatwoot.website_token', ''),
            'chatwoot_admin_url' => (string) config('chatwoot.base_url', 'http://localhost:3000'),
            'crm_url' => (string) config('crm.base_url', 'http://localhost:3002'),
            'dify_url' => 'http://localhost:8080',
            'chatdify_url' => 'http://localhost:8000',
            'launcher_title' => (string) config('chatwoot.launcher_title', 'Chat with us'),
            'welcome_title' => (string) config('chatwoot.welcome_title', 'Store Support'),
            'welcome_description' => (string) config('chatwoot.welcome_description', ''),
            'widget_position' => (string) config('chatwoot.position', 'right'),
            'fallback_enabled' => true,
        ];
    }

    public static function forEdit(): self
    {
        return self::query()->firstOrNew(['id' => 1], self::emptyDefaults());
    }

    public static function stored(): ?self
    {
        return self::query()->find(1);
    }
}
