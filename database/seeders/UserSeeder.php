<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin users
        $adminUsers = [
            [
                'name' => 'Admin User',
                'email' => 'admin@monsieur-wifi.com',
                'password' => Hash::make('abcd1234'),
                'role' => 'admin',
            ],
            [
                'name' => 'Administrator',
                'email' => 'administrator@monsieur-wifi.com',
                'password' => Hash::make('abcd1234'),
                'role' => 'admin',
            ],
        ];

        foreach ($adminUsers as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
