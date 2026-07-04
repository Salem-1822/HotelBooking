<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Admin::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Super Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role' => 'super_admin',
                'status' => 'active'
            ]
        );

        \App\Models\Admin::updateOrCreate(
            ['email' => 'admin2@test.com'],
            [
                'name' => 'Admin User',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role' => 'admin',
                'status' => 'active'
            ]
        );

        \App\Models\Admin::updateOrCreate(
            ['email' => 'client@test.com'],
            [
                'name' => 'Client User',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role' => 'client',
                'status' => 'active'
            ]
        );
    }
}
