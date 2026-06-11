<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Role;
use App\Models\Setting;
use App\Models\StoreType;
use App\Models\Subcategory;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        ProductImage::truncate();
        Product::truncate();
        Subcategory::truncate();
        Vendor::truncate();
        StoreType::truncate();
        User::truncate();
        Admin::truncate();
        Setting::truncate();
        Role::truncate();
        Governorate::truncate();
        Country::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call([
            CountrySeeder::class,
            GovernorateSeeder::class,
            RoleSeeder::class,
            AdminSeeder::class,
            SettingsSeeder::class,
            UserSeeder::class,
            StoreTypeSeeder::class,
            SubcategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
