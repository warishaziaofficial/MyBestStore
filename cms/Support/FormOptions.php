<?php

namespace Cms\Support;

use Cms\Models\BlogCategory;
use Cms\Models\Brand;
use Cms\Models\Category;
use Cms\Models\Product;

class FormOptions
{
    public static function forField(array $field): array
    {
        return match ($field['source'] ?? null) {
            'categories' => Category::query()->orderBy('name')->get()
                ->map(fn (Category $category) => [
                    'value' => $category->slug,
                    'label' => $category->name,
                ])->all(),
            'brands' => Brand::query()->orderBy('name')->get()
                ->map(fn (Brand $brand) => [
                    'value' => $brand->name,
                    'label' => $brand->name,
                ])->all(),
            'products' => Product::query()->orderBy('name')->get()
                ->map(fn (Product $product) => [
                    'value' => (string) $product->id,
                    'label' => $product->name.' (#'.$product->id.')',
                ])->all(),
            'blog-categories' => BlogCategory::query()->orderBy('label')->get()
                ->map(fn (BlogCategory $category) => [
                    'value' => $category->label,
                    'label' => $category->label,
                ])->all(),
            default => [],
        };
    }

    public static function forConfig(array $fields): array
    {
        $options = [];

        foreach ($fields as $name => $field) {
            if (($field['type'] ?? '') === 'relation_select') {
                $options[$name] = self::forField($field);
            }
        }

        return $options;
    }
}
