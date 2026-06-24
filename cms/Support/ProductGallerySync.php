<?php

namespace Cms\Support;

use Cms\Models\Media;
use Cms\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProductGallerySync
{
    /**
     * @return \Illuminate\Support\Collection<int, ProductImage>
     */
    public static function forProduct(int $productId)
    {
        if (! Schema::hasTable('ProductImages')) {
            return collect();
        }

        return ProductImage::query()
            ->where('product_id', $productId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public static function sync(int $productId, Request $request): void
    {
        if (! Schema::hasTable('ProductImages')) {
            return;
        }

        $removeIds = collect($request->input('gallery_remove', []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        if ($removeIds !== []) {
            ProductImage::query()
                ->where('product_id', $productId)
                ->whereIn('id', $removeIds)
                ->delete();
        }

        foreach ($request->input('gallery_existing', []) as $id => $row) {
            $imageId = (int) $id;
            if ($imageId <= 0) {
                continue;
            }

            ProductImage::query()
                ->where('product_id', $productId)
                ->where('id', $imageId)
                ->update([
                    'alt_text' => trim((string) ($row['alt_text'] ?? '')) ?: null,
                    'sort_order' => (int) ($row['sort_order'] ?? 0),
                ]);
        }

        $newRows = $request->input('gallery_new', []);
        if (! is_array($newRows)) {
            return;
        }

        foreach ($newRows as $index => $row) {
            $path = trim((string) ($row['image'] ?? ''));

            if ($request->hasFile("gallery_new.{$index}.image_file")) {
                $stored = MediaStorage::store($request->file("gallery_new.{$index}.image_file"));
                Media::create(array_merge($stored, [
                    'alt_text' => trim((string) ($row['alt_text'] ?? '')) ?: null,
                ]));
                $path = $stored['path'];
            }

            if ($path === '') {
                continue;
            }

            ProductImage::create([
                'product_id' => $productId,
                'image' => $path,
                'alt_text' => trim((string) ($row['alt_text'] ?? '')) ?: null,
                'sort_order' => (int) ($row['sort_order'] ?? $index),
            ]);
        }
    }
}
