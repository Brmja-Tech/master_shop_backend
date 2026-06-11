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
            'سوبر ماركت' => [
                'مخبوزات',
                'ألبان',
                'خضروات وفاكهة',
            ],
            'صيدلية' => [
                'مسكنات',
                'عناية بالبشرة',
                'فيتامينات',
            ],
            'مطعم' => [
                'وجبات',
                'مقبلات',
                'مشروبات',
            ],
            'كافيه' => [
                'قهوة',
                'حلويات',
                'عصائر',
            ],
        ];

        foreach ($subcategoriesByStoreType as $storeTypeName => $subcategories) {
            $storeType = StoreType::query()
                ->where('name', $storeTypeName)
                ->first();

            if (! $storeType) {
                continue;
            }

            foreach ($subcategories as $subcategoryName) {
                Subcategory::query()->firstOrCreate(
                    [
                        'store_type_id' => $storeType->id,
                        'name' => $subcategoryName,
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
