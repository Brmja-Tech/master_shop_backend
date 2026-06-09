<?php

namespace Database\Seeders;

use App\Models\StoreType;
use Illuminate\Database\Seeder;

class StoreTypeSeeder extends Seeder
{
    public function run(): void
    {
        StoreType::insert([
            [
                'name_ar' => 'سوبر ماركت',
                'name_en' => 'Supermarket',
            ],
            [
                'name_ar' => 'صيدلية',
                'name_en' => 'Pharmacy',
            ],
            [
                'name_ar' => 'مطعم',
                'name_en' => 'Restaurant',
            ],
            [
                'name_ar' => 'كافيه',
                'name_en' => 'Cafe',
            ],
        ]);
    }
}
