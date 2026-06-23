<?php

namespace App\Services;

use App\Models\ShippingRate;
use App\Models\ShippingZone;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class ShippingService
{
    /**
     * @param  array{country?: string, province?: string, city?: string, subtotal?: int, discount?: int, weight?: float}  $context
     * @return array{
     *     available: bool,
     *     zone: string|null,
     *     zone_id: int|null,
     *     weight: float,
     *     subtotal: int,
     *     discount: int,
     *     methods: array<int, array<string, mixed>>,
     *     message: string|null
     * }
     */
    public function quote(array $context): array
    {
        $country = trim((string) ($context['country'] ?? ''));
        $province = trim((string) ($context['province'] ?? ''));
        $city = trim((string) ($context['city'] ?? ''));
        $subtotal = (int) ($context['subtotal'] ?? 0);
        $discount = (int) ($context['discount'] ?? 0);
        $weight = max(0, (float) ($context['weight'] ?? 0));

        if ($country === '' || $city === '') {
            return $this->emptyQuote($subtotal, $discount, $weight, 'Enter your delivery address to view shipping methods.');
        }

        $zone = $this->resolveZone($country, $province, $city);
        $freeThreshold = (int) config('shipping.free_shipping_threshold', 10000);

        if ($subtotal >= $freeThreshold) {
            return [
                'available' => true,
                'zone' => $zone?->name,
                'zone_id' => $zone?->id,
                'weight' => $weight,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'methods' => [[
                    'key' => 'free_shipping',
                    'name' => config('shipping.methods.free_shipping.label', 'Free Shipping'),
                    'estimate' => config('shipping.methods.free_shipping.estimate', '3–5 working days'),
                    'description' => config('shipping.methods.free_shipping.description', 'For orders above Rs 10,000'),
                    'amount' => 0,
                    'formatted_amount' => 'Rs 0',
                ]],
                'message' => null,
            ];
        }

        $rates = $this->ratesForZone($zone);
        $weightExtra = $this->weightExtra($weight, $rates);
        $methods = [];

        foreach (['standard_delivery', 'express_delivery'] as $methodKey) {
            $rate = $rates->first(fn (ShippingRate $rate) => $rate->method_name === $methodKey);

            if (! $rate) {
                continue;
            }

            $base = (int) round((float) $rate->base_rate);
            $expressExtra = $methodKey === 'express_delivery'
                ? (int) round((float) ($rate->extra_rate ?? config('shipping.fallback_express_extra', 200)))
                : 0;

            $amount = $base + $expressExtra + $weightExtra;
            $meta = config('shipping.methods.'.$methodKey, []);

            $methods[] = [
                'key' => $methodKey,
                'name' => $meta['label'] ?? ucwords(str_replace('_', ' ', $methodKey)),
                'estimate' => $meta['estimate'] ?? '',
                'description' => null,
                'amount' => $amount,
                'formatted_amount' => 'Rs '.number_format($amount),
            ];
        }

        if ($methods === []) {
            $fallback = (int) config('shipping.fallback_standard_rate', 250);
            $expressExtra = (int) config('shipping.fallback_express_extra', 200);

            $methods = [
                $this->buildMethod('standard_delivery', $fallback + $weightExtra),
                $this->buildMethod('express_delivery', $fallback + $expressExtra + $weightExtra),
            ];
        }

        return [
            'available' => $methods !== [],
            'zone' => $zone?->name ?? 'Default',
            'zone_id' => $zone?->id,
            'weight' => $weight,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'methods' => $methods,
            'message' => $methods === [] ? 'Shipping is not available for this location. Please contact support.' : null,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function resolveMethod(array $context, string $methodKey): ?array
    {
        $quote = $this->quote($context);

        if (! $quote['available']) {
            return null;
        }

        foreach ($quote['methods'] as $method) {
            if (($method['key'] ?? '') === $methodKey) {
                return $method;
            }
        }

        return null;
    }

    public function resolveZone(string $country, string $province, string $city): ?ShippingZone
    {
        if (! Schema::hasTable('shipping_zones')) {
            return null;
        }

        $zones = ShippingZone::query()->active()->where('country', $country)->get();

        if ($zones->isEmpty()) {
            return null;
        }

        if ($city !== '') {
            $cityMatch = $zones->first(function (ShippingZone $zone) use ($city, $province) {
                if (! $zone->city || strcasecmp($zone->city, $city) !== 0) {
                    return false;
                }

                if ($zone->province && $province !== '' && strcasecmp($zone->province, $province) !== 0) {
                    return false;
                }

                return true;
            });

            if ($cityMatch) {
                return $cityMatch;
            }
        }

        if ($province !== '') {
            $remoteMatch = $zones->first(fn (ShippingZone $zone) => $zone->is_remote
                && ! $zone->city
                && $zone->province
                && strcasecmp($zone->province, $province) === 0);

            if ($remoteMatch) {
                return $remoteMatch;
            }

            $provinceMatch = $zones->first(fn (ShippingZone $zone) => ! $zone->city
                && ! $zone->is_remote
                && $zone->province
                && strcasecmp($zone->province, $province) === 0);

            if ($provinceMatch) {
                return $provinceMatch;
            }
        }

        $remoteDefault = $zones->first(fn (ShippingZone $zone) => $zone->is_remote && ! $zone->city && ! $zone->province);
        if ($remoteDefault) {
            return $remoteDefault;
        }

        return $zones->first(fn (ShippingZone $zone) => ! $zone->city && ! $zone->province && ! $zone->is_remote)
            ?? $zones->first(fn (ShippingZone $zone) => ! $zone->city);
    }

    /**
     * @return Collection<int, ShippingRate>
     */
    private function ratesForZone(?ShippingZone $zone): Collection
    {
        if (! $zone) {
            return collect();
        }

        return $zone->rates()->active()->get();
    }

    /**
     * @param  Collection<int, ShippingRate>  $rates
     */
    private function weightExtra(float $weight, Collection $rates): int
    {
        foreach ($rates as $rate) {
            if ($rate->min_weight !== null || $rate->max_weight !== null) {
                $min = (float) ($rate->min_weight ?? 0);
                $max = $rate->max_weight !== null ? (float) $rate->max_weight : INF;

                if ($weight > $min && $weight <= $max) {
                    return (int) round((float) ($rate->extra_rate ?? 0));
                }
            }
        }

        if ($weight <= 1) {
            return 0;
        }

        if ($weight <= 3) {
            return 150;
        }

        if ($weight <= 5) {
            return 300;
        }

        return 500;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildMethod(string $key, int $amount): array
    {
        $meta = config('shipping.methods.'.$key, []);

        return [
            'key' => $key,
            'name' => $meta['label'] ?? ucwords(str_replace('_', ' ', $key)),
            'estimate' => $meta['estimate'] ?? '',
            'description' => null,
            'amount' => $amount,
            'formatted_amount' => 'Rs '.number_format($amount),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyQuote(int $subtotal, int $discount, float $weight, ?string $message): array
    {
        return [
            'available' => false,
            'zone' => null,
            'zone_id' => null,
            'weight' => $weight,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'methods' => [],
            'message' => $message,
        ];
    }
}
