<?php

namespace App\Support;

use Cms\Models\ChatSettings;
use Illuminate\Support\Facades\Schema;

class ChatwootSettings
{
    /** @return array<string, mixed> */
    public static function resolved(): array
    {
        $settings = [
            'enabled' => (bool) config('chatwoot.enabled', false),
            'base_url' => rtrim((string) config('chatwoot.base_url', 'http://localhost:3000'), '/'),
            'website_token' => (string) config('chatwoot.website_token', ''),
            'chatwoot_admin_url' => rtrim((string) config('chatwoot.base_url', 'http://localhost:3000'), '/'),
            'crm_url' => rtrim((string) config('crm.base_url', ''), '/'),
            'dify_url' => 'http://localhost:8080',
            'chatdify_url' => 'http://localhost:8000',
            'launcher_title' => (string) config('chatwoot.launcher_title', 'Chat with us'),
            'welcome_title' => (string) config('chatwoot.welcome_title', 'Store Support'),
            'welcome_description' => (string) config('chatwoot.welcome_description', ''),
            'position' => (string) config('chatwoot.position', 'right'),
            'fallback_enabled' => true,
        ];

        if (! Schema::hasTable('ChatSettings')) {
            return $settings;
        }

        $row = ChatSettings::stored();

        if (! $row) {
            return $settings;
        }

        return [
            'enabled' => (bool) $row->is_enabled,
            'base_url' => rtrim((string) $row->base_url, '/'),
            'website_token' => (string) $row->website_token,
            'chatwoot_admin_url' => rtrim((string) ($row->chatwoot_admin_url ?: $row->base_url), '/'),
            'crm_url' => rtrim((string) ($row->crm_url ?: config('crm.base_url', '')), '/'),
            'dify_url' => rtrim((string) ($row->dify_url ?: 'http://localhost:8080'), '/'),
            'chatdify_url' => rtrim((string) ($row->chatdify_url ?: 'http://localhost:8000'), '/'),
            'launcher_title' => (string) $row->launcher_title,
            'welcome_title' => (string) $row->welcome_title,
            'welcome_description' => (string) ($row->welcome_description ?? ''),
            'position' => in_array($row->widget_position, ['left', 'right'], true) ? $row->widget_position : 'right',
            'fallback_enabled' => (bool) $row->fallback_enabled,
        ];
    }

    /** @return array<string, mixed> */
    public static function forStorefront(): array
    {
        return self::resolved();
    }

    public static function isOnline(?string $baseUrl = null): bool
    {
        $baseUrl = rtrim((string) ($baseUrl ?: self::resolved()['base_url']), '/');

        if ($baseUrl === '') {
            return false;
        }

        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 3,
                    'ignore_errors' => true,
                ],
            ]);

            $response = @file_get_contents($baseUrl, false, $context);

            return $response !== false;
        } catch (\Throwable) {
            return false;
        }
    }
}
