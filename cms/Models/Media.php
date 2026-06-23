<?php

namespace Cms\Models;

class Media extends CmsModel
{
    protected $table = 'Media';

    protected $fillable = [
        'filename',
        'path',
        'alt_text',
        'mime_type',
        'size_bytes',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
    ];
}
