<?php

namespace Database\Seeders;

use App\Models\CourierCompany;
use Illuminate\Database\Seeder;

class CourierCompanySeeder extends Seeder
{
    public function run(): void
    {
        if (CourierCompany::query()->exists()) {
            return;
        }

        $companies = [
            ['name' => 'TCS', 'tracking_url' => 'https://www.tcsexpress.com/track/{tracking}'],
            ['name' => 'Leopards Courier', 'tracking_url' => 'https://leopardscourier.com/track/{tracking}'],
            ['name' => 'M&P', 'tracking_url' => 'https://www.mulphico.pk/track/{tracking}'],
            ['name' => 'PostEx', 'tracking_url' => 'https://postex.pk/track/{tracking}'],
            ['name' => 'Trax', 'tracking_url' => 'https://trax.pk/track/{tracking}'],
            ['name' => 'Call Courier', 'tracking_url' => 'https://callcourier.com.pk/track/{tracking}'],
        ];

        foreach ($companies as $company) {
            CourierCompany::query()->create([
                'name' => $company['name'],
                'tracking_url' => $company['tracking_url'],
                'status' => 'active',
            ]);
        }
    }
}
