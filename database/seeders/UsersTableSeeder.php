<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Example: create 5 users
        for ($i = 1; $i <= 5; $i++) {
            DB::table('users')->insert([
                'name' => 'User ' . $i,
                'mobile_number' => '0171000000' . $i, // unique mobile numbers
                'username' => 'user' . $i,
                'email' => 'user' . $i . '@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'), // hashed password
                'status' => 'active',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Optional: add an admin user
        DB::table('users')->insert([
            'name' => 'Admin',
            'mobile_number' => '01710000000',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'status' => 1,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
