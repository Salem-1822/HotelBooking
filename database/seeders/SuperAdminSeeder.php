<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds or updates the Super Admin account in both Admin and User models
     * ensuring the role is set to 'super_admin' and password is appropriately hashed.
     */
    public function run(): void
    {
        $email = 'salemzaafrou@gmail.com';
        $hashedPassword = Hash::make('password123');

        // Ensure Super Admin in Admin model / admins table
        Admin::updateOrCreate(
            ['email' => $email],
            [
                'name'     => 'Super Admin',
                'password' => $hashedPassword,
                'role'     => 'super_admin',
                'status'   => 'active',
            ]
        );

        // Ensure Super Admin in User model / users table
        User::updateOrCreate(
            ['email' => $email],
            [
                'name'     => 'Super Admin',
                'password' => $hashedPassword,
                'role'     => 'super_admin',
                'status'   => 'active',
            ]
        );
    }
}
