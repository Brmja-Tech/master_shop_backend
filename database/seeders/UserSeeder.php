<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([
            [
                'image'             => 'uploads/images/image.png',
                'name'              => 'Ahmed Mohamed',
                'email'             => 'ahmed@gmail.com',
                'phone'             => '01012345678',
                'email_verified_at' => now(),
                'password'          => bcrypt('password'),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'image'             => 'uploads/images/image.png',
                'name'              => 'Mohamed Ali',
                'email'             => 'mohamed@gmail.com',
                'phone'             => '01112345678',
                'email_verified_at' => now(),
                'password'          => bcrypt('password'),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'image'             => 'uploads/images/image.png',
                'name'              => 'Sara Ahmed',
                'email'             => 'sara@gmail.com',
                'phone'             => '01212345678',
                'email_verified_at' => now(),
                'password'          => bcrypt('password'),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'image'             => 'uploads/images/image.png',
                'name'              => 'Omar Hassan',
                'email'             => 'omar@gmail.com',
                'phone'             => '01512345678',
                'email_verified_at' => now(),
                'password'          => bcrypt('password'),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'image'             => 'uploads/images/image.png',
                'name'              => 'Mona Khaled',
                'email'             => 'mona@gmail.com',
                'phone'             => '01098765432',
                'email_verified_at' => now(),
                'password'          => bcrypt('password'),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ]);
    }
}

