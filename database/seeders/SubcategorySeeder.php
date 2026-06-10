<?php

namespace Database\Seeders;

use App\Models\StoreType;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class SubcategorySeeder extends Seeder
{
    public function run(): void
    {
        $subcategoriesByStoreType = [
            'Supermarket' => [
                ['ar' => 'مخبوزات', 'en' => 'Bakery'],
                ['ar' => 'ألبان', 'en' => 'Dairy'],
                ['ar' => 'خضروات وفاكهة', 'en' => 'Fruits & Vegetables'],
            ],
            'Pharmacy' => [
                ['ar' => 'مسكنات', 'en' => 'Pain Relief'],
                ['ar' => 'عناية بالبشرة', 'en' => 'Skin Care'],
                ['ar' => 'فيتامينات', 'en' => 'Vitamins'],
            ],
            'Restaurant' => [
                ['ar' => 'وجبات', 'en' => 'Meals'],
                ['ar' => 'مقبلات', 'en' => 'Appetizers'],
                ['ar' => 'مشروبات', 'en' => 'Drinks'],
            ],
            'Cafe' => [
                ['ar' => 'قهوة', 'en' => 'Coffee'],
                ['ar' => 'حلويات', 'en' => 'Desserts'],
                ['ar' => 'عصائر', 'en' => 'Juices'],
            ],
        ];

        foreach ($subcategoriesByStoreType as $storeTypeEn => $subcategories) {
            $storeType = StoreType::query()
                ->where('name->en', $storeTypeEn)
                ->first();

            if (! $storeType) {
                continue;
            }

            foreach ($subcategories as $subcategoryName) {
                Subcategory::query()->firstOrCreate(
                    [
                        'store_type_id' => $storeType->id,
                        'name->en' => $subcategoryName['en'],
                    ],
                    [
                        'store_type_id' => $storeType->id,
                        'vendor_id' => null,
                        'name' => $subcategoryName,
                    ]
                );
            }
        }
    }
}
