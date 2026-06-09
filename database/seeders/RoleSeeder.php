<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = array_keys(config('permessions_ar'));

        Role::create([
            'role' => [
                'ar' => 'المدير',
                'en' => 'manger',
            ],
            'permession' => json_encode($permissions),
        ]);
    }

}
