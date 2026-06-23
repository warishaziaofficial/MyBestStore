<?php

namespace Cms\Support;

class SocialPlatforms
{
    public const ORDER_SOURCES = [
        'website',
        'facebook',
        'instagram',
        'tiktok',
        'whatsapp',
        'other',
    ];

    public const ACCOUNT_PLATFORMS = [
        'instagram',
        'facebook',
        'tiktok',
        'whatsapp',
        'other',
    ];

    public static function isSocialSource(string $source): bool
    {
        return $source !== 'website';
    }

    public static function normalizePlatform(string $platform): string
    {
        $platform = strtolower(trim($platform));

        if ($platform === 'web' || $platform === 'storefront') {
            return 'website';
        }

        return in_array($platform, self::ORDER_SOURCES, true) ? $platform : 'other';
    }
}
