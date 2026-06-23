<?php

namespace Cms\Models;

use Illuminate\Support\Str;

class Brand extends CmsModel
{
    protected $table = 'Brands';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['id', 'name', 'logo', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Brand $brand): void {
            if (filled($brand->getKey())) {
                return;
            }

            $id = Str::slug((string) $brand->name);

            if ($id === '') {
                $id = 'brand-'.Str::lower(Str::random(8));
            }

            $base = $id;
            $suffix = 2;

            while (static::query()->whereKey($id)->exists()) {
                $id = $base.'-'.$suffix;
                $suffix++;
            }

            $brand->setAttribute($brand->getKeyName(), $id);
        });
    }
}
