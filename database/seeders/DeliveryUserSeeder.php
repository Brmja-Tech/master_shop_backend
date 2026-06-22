<?php

namespace Database\Seeders;

use App\Models\DeliveryUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DeliveryUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DeliveryUser::updateOrCreate(
            ['phone' => '01029292929'],
            [
                'name' => 'Completion Captain',
                'email' => 'completion-captain@example.com',
                'password' => Hash::make('password123'),
                'front_ident' => 'delivaries/front11.png',
                'back_ident' => 'delivaries/back11.png',
                'personal_deriving_license' => 'delivaries/personal11.png',
                'machine_license' => 'delivaries/machine11.png',
                'approval_status' => 'approved',
                'active_status' => true,
                'ban' => false,
                'lat' => 30.0450000,
                'lng' => 31.2360000,
                'max_active_orders' => 1,
            ]
        );
    }
}
