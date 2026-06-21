<?php

namespace Database\Seeders;

use App\Models\StoreType;
use Illuminate\Database\Seeder;

class StoreTypeSeeder extends Seeder
{
    public function run(): void
    {
        $defaultImage = 'uploads/images/logo.jpeg';

        $storeTypes = [
            [
                'name' => 'سوبر ماركت',
                'image' => $defaultImage,
            ],
            [
                'name' => 'صيدلية',
                'image' => $defaultImage,
            ],
            [
                'name' => 'مطعم',
                'image' => $defaultImage,
            ],
            [
                'name' => 'كافيه',
                'image' => $defaultImage,
            ],
        ];

        foreach ($storeTypes as $storeType) {
            StoreType::query()->updateOrCreate(
                ['name' => $storeType['name']],
                [
                    'name' => $storeType['name'],
                    'image' => $storeType['image'],
                ]
            );
        }
    }
}
