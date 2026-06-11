<?php

namespace Database\Seeders;

use App\Models\StoreType;
use Illuminate\Database\Seeder;

class StoreTypeSeeder extends Seeder
{
    public function run(): void
    {
        $defaultImage = 'uploads/images/logo.png';

        $storeTypes = [
            [
                'name' => [
                    'ar' => 'سوبر ماركت',
                    'en' => 'Supermarket',
                ],
                'image' => $defaultImage,
            ],
            [
                'name' => [
                    'ar' => 'صيدلية',
                    'en' => 'Pharmacy',
                ],
                'image' => $defaultImage,
            ],
            [
                'name' => [
                    'ar' => 'مطعم',
                    'en' => 'Restaurant',
                ],
                'image' => $defaultImage,
            ],
            [
                'name' => [
                    'ar' => 'كافيه',
                    'en' => 'Cafe',
                ],
                'image' => $defaultImage,
            ],
        ];

        foreach ($storeTypes as $storeType) {
            StoreType::query()->updateOrCreate(
                ['name->en' => $storeType['name']['en']],
                [
                    'name' => $storeType['name'],
                    'image' => $storeType['image'],
                ]
            );
        }
    }
}
