<?php

namespace Scripts\Lib;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CmsSchemaBootstrap
{
    public static function ensureCatalogTables(): void
    {
        if (! self::hasExactTable('Categories')) {
            Schema::create('Categories', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('slug', 100)->unique();
                $table->integer('count')->default(0);
                $table->string('image', 500);
                $table->string('image_alt', 255);
                $table->text('description');
                $table->timestamps();
            });
        }

        if (! self::hasExactTable('Products')) {
            Schema::create('Products', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->integer('price')->default(0);
                $table->integer('old_price')->nullable();
                $table->string('image', 500);
                $table->string('image_alt', 255);
                $table->string('category', 100);
                $table->string('sub_category', 100)->default('');
                $table->longText('description')->nullable();
                $table->decimal('rating', 2, 1)->default(4.5);
                $table->integer('review_count')->default(0);
                $table->string('badge', 20)->nullable();
                $table->boolean('featured')->default(false);
                $table->string('brand', 100)->default('');
                $table->integer('stock')->default(0);
                $table->timestamps();
            });
        }

        if (! self::hasExactTable('ProductImages')) {
            Schema::create('ProductImages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->string('image', 500);
                $table->string('alt_text', 255)->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index('product_id');
            });
        }
    }

    public static function hasExactTable(string $table): bool
    {
        $row = DB::selectOne(
            "SELECT name FROM sqlite_master WHERE type = 'table' AND name = ?",
            [$table]
        );

        return $row !== null;
    }
}
