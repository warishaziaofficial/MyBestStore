<?php

namespace App\Services;

use App\Support\StorefrontData;
use Illuminate\Support\Facades\Session;

class WishlistService
{
    private string $sessionKey;

    public function __construct()
    {
        $this->sessionKey = config('wishlist.session_key', 'mbs_wishlist');
    }

    /**
     * @return array<int, string>
     */
    public function slugs(): array
    {
        return array_values(Session::get($this->sessionKey, []));
    }

    public function count(): int
    {
        return count($this->slugs());
    }

    /**
     * @return array<int, string>
     */
    public function toggle(string $slug): array
    {
        if (! StorefrontData::findBySlug($slug)) {
            return $this->slugs();
        }

        $slugs = $this->slugs();

        if (in_array($slug, $slugs, true)) {
            $slugs = array_values(array_filter($slugs, fn (string $item) => $item !== $slug));
        } else {
            $slugs[] = $slug;
        }

        Session::put($this->sessionKey, $slugs);

        return $slugs;
    }

    /**
     * @return array<int, string>
     */
    public function remove(string $slug): array
    {
        $slugs = array_values(array_filter(
            $this->slugs(),
            fn (string $item) => $item !== $slug
        ));

        Session::put($this->sessionKey, $slugs);

        return $slugs;
    }

    /**
     * @param  array<int, string>  $incoming
     * @return array<int, string>
     */
    public function merge(array $incoming): array
    {
        $valid = [];

        foreach ($incoming as $slug) {
            if (is_string($slug) && StorefrontData::findBySlug($slug)) {
                $valid[] = $slug;
            }
        }

        $merged = array_values(array_unique(array_merge($this->slugs(), $valid)));
        Session::put($this->sessionKey, $merged);

        return $merged;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function products(): array
    {
        $products = [];

        foreach ($this->slugs() as $slug) {
            $product = StorefrontData::findBySlug($slug);

            if ($product) {
                $products[] = $product;
            }
        }

        return $products;
    }
}
