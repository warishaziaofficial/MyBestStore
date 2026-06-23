<?php

namespace Cms\Models;

class FooterSettings extends CmsModel
{
    protected $table = 'FooterSettings';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = [
        'tagline',
        'website_url',
        'instagram_url',
        'facebook_url',
        'copyright_text',
        'newsletter_heading',
        'newsletter_text',
    ];

    public static function emptyDefaults(): array
    {
        return [
            'tagline' => '',
            'website_url' => '',
            'instagram_url' => null,
            'facebook_url' => null,
            'copyright_text' => '',
            'newsletter_heading' => 'Newsletter',
            'newsletter_text' => '',
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
