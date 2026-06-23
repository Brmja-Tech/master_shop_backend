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
                'image' => 'uploads/store-types/supermarket.png',
            ],
            [
                'name' => 'صيدلية',
                'image' => 'uploads/store-types/pharmacy.png',
            ],
            [
                'name' => 'مطعم',
                'image' => 'uploads/store-types/restaurant.png',
            ],
            [
                'name' => 'كافيه',
                'image' => 'uploads/store-types/cafe.png',
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
