<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@docratech.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('Super Admin');

        // Create Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@docratech.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('Admin');

        // Create test admin user (commonly used email)
        $testAdmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'first_name' => 'Test',
                'last_name' => 'Admin',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $testAdmin->assignRole('Super Admin');

        // Create Manager user
        $manager = User::firstOrCreate(
            ['email' => 'manager@docratech.com'],
            [
                'first_name' => 'Manager',
                'last_name' => 'User',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $manager->assignRole('Manager');

        // Create regular user
        $user = User::firstOrCreate(
            ['email' => 'user@docratech.com'],
            [
                'first_name' => 'Regular',
                'last_name' => 'User',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $user->assignRole('User');

        // Create additional test users
        $testUsers = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@docratech.com',
                'role' => 'Manager',
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@docratech.com',
                'role' => 'User',
            ],
            [
                'first_name' => 'Mike',
                'last_name' => 'Johnson',
                'email' => 'mike.johnson@docratech.com',
                'role' => 'User',
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Wilson',
                'email' => 'sarah.wilson@docratech.com',
                'role' => 'Manager',
            ],
        ];

        foreach ($testUsers as $testUser) {
            $user = User::firstOrCreate(
                ['email' => $testUser['email']],
                [
                    'first_name' => $testUser['first_name'],
                    'last_name' => $testUser['last_name'],
                    'password' => Hash::make('password'),
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole($testUser['role']);
        }
    }
}
