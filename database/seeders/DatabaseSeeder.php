<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            GovernorateSeeder::class,
            RoleSeeder::class,
            AdminSeeder::class,
            SettingsSeeder::class,
            UserSeeder::class,
        ]);
    }
}
