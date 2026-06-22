<?php

namespace App\Support;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class Mbs
{
    public static function image(?string $path): string
    {
        if (empty($path)) {
            return asset('placeholder-product.svg');
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset(ltrim($path, '/'));
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
