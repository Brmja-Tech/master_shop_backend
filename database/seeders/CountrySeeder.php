<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        Country::updateOrCreate(
            ['name->en' => 'Egypt'],
            [
                'name'      => ['ar' => 'مصر', 'en' => 'Egypt'],
                'status' => true,
            ]
        );

        Country::updateOrCreate(
            ['name->en' => 'Saudi Arabia'],
            [
                'name'      => ['ar' => 'السعودية', 'en' => 'Saudi Arabia'],
                'status' => true,
            ]
        );
    }
}
