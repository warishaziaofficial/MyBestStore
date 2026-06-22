<?php

namespace Database\Seeders;

use App\Models\ShippingRate;
use App\Models\ShippingZone;
use Illuminate\Database\Seeder;

class ShippingSeeder extends Seeder
{
    public function run(): void
    {
        if (ShippingZone::query()->exists()) {
            return;
        }

        $majorCities = ['Lahore', 'Karachi', 'Islamabad', 'Faisalabad'];

        foreach ($majorCities as $city) {
            $zone = ShippingZone::query()->create([
                'name' => $city,
                'country' => 'Pakistan',
                'province' => null,
                'city' => $city,
                'is_remote' => false,
                'status' => 'active',
            ]);

            $this->seedRates($zone, 250);
        }

        $otherZone = ShippingZone::query()->create([
            'name' => 'Other Cities (Pakistan)',
            'country' => 'Pakistan',
            'province' => null,
            'city' => null,
            'is_remote' => false,
            'status' => 'active',
        ]);

        $this->seedRates($otherZone, 350);

        $remoteZone = ShippingZone::query()->create([
            'name' => 'Remote Areas (Pakistan)',
            'country' => 'Pakistan',
            'province' => null,
            'city' => null,
            'is_remote' => true,
            'status' => 'active',
        ]);

        $this->seedRates($remoteZone, 500);
    }

    private function seedRates(ShippingZone $zone, int $standardBase): void
    {
        ShippingRate::query()->create([
            'shipping_zone_id' => $zone->id,
            'method_name' => 'standard_delivery',
            'base_rate' => $standardBase,
            'status' => 'active',
        ]);

        ShippingRate::query()->create([
            'shipping_zone_id' => $zone->id,
            'method_name' => 'express_delivery',
            'base_rate' => $standardBase,
            'extra_rate' => 200,
            'status' => 'active',
        ]);
    }
}
