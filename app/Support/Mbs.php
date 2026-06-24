<?php

namespace App\Support;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;

class Mbs
{
    public static function storeName(): string
    {
        return (string) config('storefront.store_name', 'DigitalWares');
    }

    public static function storeDomain(): string
    {
        return (string) config('storefront.store_domain', 'digitalwares.pk');
    }

    public static function storeLabel(): string
    {
        return self::storeName().'.'.self::storeDomain();
    }

    public static function sanitizeProductHtml(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $clean = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $clean = preg_replace('/<(script|iframe|style|form|input|textarea|select|button|object|embed|link|meta)[^>]*>.*?<\/\1>/is', '', $clean) ?? $clean;
        $clean = preg_replace('/<(script|iframe|style|form|input|textarea|select|button|object|embed|link|meta)\b[^>]*\/?>/i', '', $clean) ?? $clean;

        $allowed = '<p><br><strong><b><ul><ol><li><span>';
        $clean = strip_tags($clean, $allowed);
        $clean = preg_replace('/\s+(on\w+|style|class|id)\s*=\s*("([^"]*)"|\'([^\']*)\'|[^\s>]+)/i', '', $clean) ?? $clean;
        $clean = preg_replace('/<(p|span|strong|b|ul|ol|li)(\s[^>]*)?>/i', '<$1>', $clean) ?? $clean;
        $clean = preg_replace('/<p>\s*<\/p>/i', '', $clean) ?? $clean;
        $clean = preg_replace('/(\s*<br\s*\/?>\s*){3,}/i', '<br><br>', $clean) ?? $clean;

        return trim($clean);
    }

    public static function productShortDescription(?string $html, int $limit = 220): string
    {
        $plain = trim(preg_replace('/\s+/u', ' ', strip_tags(self::sanitizeProductHtml($html))) ?? '');

        if ($plain === '') {
            return 'Premium digital hardware with official warranty and fast delivery across Pakistan.';
        }

        return Str::limit($plain, $limit);
    }
    public static function image(?string $path): string
    {
        if (empty($path)) {
            return asset('placeholder-product.svg');
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $relative = ltrim($path, '/');
        $fullPath = public_path($relative);

        if (! is_file($fullPath)) {
            return asset('placeholder-product.svg');
        }

        return asset($relative);
    }

    public static function price(int|float $amount): string
    {
        return 'Rs ' . number_format((int) $amount);
    }

    public static function stars(float $rating): string
    {
        $full = (int) floor($rating);
        $half = ($rating - $full) >= 0.5 ? 1 : 0;
        $empty = 5 - $full - $half;

        return str_repeat('★', $full) . ($half ? '½' : '') . str_repeat('☆', max(0, $empty));
    }

    public static function navUrl(array $item): string
    {
        $url = ! empty($item['route']) ? route($item['href']) : ($item['href'] ?? '#');

        return $url . ($item['hash'] ?? '');
    }

    public static function shopCategoryUrl(?string $slug): string
    {
        if (empty($slug)) {
            return route('shop');
        }

        return route('shop', ['category' => $slug]);
    }

    public static function paginate(array $items, int $perPage = 12, string $pageName = 'page'): LengthAwarePaginator
    {
        $page = Paginator::resolveCurrentPage($pageName);
        $total = count($items);
        $results = array_slice($items, ($page - 1) * $perPage, $perPage);

        return new LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
                'pageName' => $pageName,
            ]
        );
    }
}
