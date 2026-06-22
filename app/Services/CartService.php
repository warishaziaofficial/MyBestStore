<?php

namespace App\Services;

use App\Models\Product;
use App\Support\StorefrontData;
use Illuminate\Support\Facades\Session;

class CartService
{
    private string $sessionKey;

    public function __construct()
    {
        $this->sessionKey = config('cart.session_key', 'mbs_cart');
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function items(): array
    {
        return Session::get($this->sessionKey.'.items', []);
    }

    public function count(): int
    {
        return (int) array_sum(array_column($this->items(), 'quantity'));
    }

    public function subtotal(): int
    {
        return (int) array_sum(array_map(
            fn (array $item) => (int) $item['price'] * (int) $item['quantity'],
            $this->items()
        ));
    }

    public function shipping(): int
    {
        if ($this->count() === 0) {
            return 0;
        }

        $threshold = (int) config('cart.free_shipping_threshold', 10000);

        return $this->subtotal() >= $threshold ? 0 : (int) config('cart.default_shipping', 500);
    }

    public function appliedCoupon(): ?string
    {
        $code = Session::get($this->sessionKey.'.coupon');

        return filled($code) ? strtoupper((string) $code) : null;
    }

    public function applyCoupon(string $code): ?string
    {
        $code = strtoupper(trim($code));
        $coupons = config('cart.coupons', []);

        if ($code === '') {
            return 'Please enter a discount code.';
        }

        if (! isset($coupons[$code])) {
            return 'This discount code is not valid.';
        }

        if ($this->count() === 0) {
            return 'Add items to your cart before applying a discount code.';
        }

        Session::put($this->sessionKey.'.coupon', $code);

        return null;
    }

    public function removeCoupon(): void
    {
        Session::forget($this->sessionKey.'.coupon');
    }

    public function discount(): int
    {
        $code = $this->appliedCoupon();

        if (! $code) {
            return 0;
        }

        $coupon = config('cart.coupons.'.$code);

        if (! is_array($coupon)) {
            return 0;
        }

        $subtotal = $this->subtotal();

        if ($subtotal === 0) {
            return 0;
        }

        return match ($coupon['type'] ?? null) {
            'percent' => (int) round($subtotal * ((float) ($coupon['value'] ?? 0) / 100)),
            'fixed' => min($subtotal, (int) ($coupon['value'] ?? 0)),
            default => 0,
        };
    }

    public function total(): int
    {
        return max(0, $this->subtotal() + $this->shipping() - $this->discount());
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function totalWeight(): float
    {
        return (float) array_sum(array_map(function (array $item) {
            $weight = (float) ($item['weight'] ?? $this->resolveProductWeight($item['slug']));

            return $weight * (int) $item['quantity'];
        }, $this->items()));
    }

    /**
     * @return array{items: array<string, array<string, mixed>>, subtotal: int, shipping: int, discount: int, total: int, count: int, coupon: string|null, weight: float}
     */
    public function summary(): array
    {
        return [
            'items' => array_values($this->items()),
            'subtotal' => $this->subtotal(),
            'shipping' => $this->shipping(),
            'discount' => $this->discount(),
            'total' => $this->total(),
            'count' => $this->count(),
            'coupon' => $this->appliedCoupon(),
            'weight' => $this->totalWeight(),
        ];
    }

    /**
     * @return array{subtotal: int, shipping: int, discount: int, total: int, weight: float}
     */
    public function checkoutTotals(int $shippingAmount): array
    {
        $subtotal = $this->subtotal();
        $discount = $this->discount();

        return [
            'subtotal' => $subtotal,
            'shipping' => max(0, $shippingAmount),
            'discount' => $discount,
            'total' => max(0, $subtotal + max(0, $shippingAmount) - $discount),
            'weight' => $this->totalWeight(),
        ];
    }

    public function add(string $slug, int $quantity = 1): bool
    {
        $product = StorefrontData::findBySlug($slug);

        if (! $product) {
            return false;
        }

        $items = $this->items();
        $key = $product['slug'];

        if (isset($items[$key])) {
            $items[$key]['quantity'] += max(1, $quantity);
        } else {
            $items[$key] = [
                'slug' => $product['slug'],
                'name' => $product['name'],
                'price' => (int) $product['price'],
                'old_price' => $product['old_price'] ?? null,
                'image' => $product['image'],
                'quantity' => max(1, $quantity),
                'weight' => (float) ($product['weight'] ?? $this->resolveProductWeight($product['slug'])),
            ];
        }

        Session::put($this->sessionKey.'.items', $items);

        return true;
    }

    public function update(string $slug, int $quantity): bool
    {
        $items = $this->items();

        if (! isset($items[$slug])) {
            return false;
        }

        if ($quantity <= 0) {
            unset($items[$slug]);
        } else {
            $items[$slug]['quantity'] = $quantity;
        }

        Session::put($this->sessionKey.'.items', $items);

        return true;
    }

    public function remove(string $slug): void
    {
        $items = $this->items();
        unset($items[$slug]);
        Session::put($this->sessionKey.'.items', $items);
    }

    public function clear(): void
    {
        Session::forget($this->sessionKey);
    }

    private function resolveProductWeight(string $slug): float
    {
        $product = Product::query()->where('slug', $slug)->value('weight');

        if ($product !== null) {
            return (float) $product;
        }

        $storeProduct = StorefrontData::findBySlug($slug);

        return (float) ($storeProduct['weight'] ?? 0);
    }
}
