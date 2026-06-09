<?php

namespace Database\Seeders;

use App\Models\StoreType;
use Illuminate\Database\Seeder;

class StoreTypeSeeder extends Seeder
{
    public function run(): void
    {
        $storeTypes = [
            [
                'ar' => 'سوبر ماركت',
                'en' => 'Supermarket',
            ],
            [
                'ar' => 'صيدلية',
                'en' => 'Pharmacy',
            ],
            [
                'ar' => 'مطعم',
                'en' => 'Restaurant',
            ],
            [
                'ar' => 'كافيه',
                'en' => 'Cafe',
            ],
        ];

        foreach ($storeTypes as $storeType) {
            StoreType::query()->create([
                'name' => $storeType,
            ]);
        }
    }
}
