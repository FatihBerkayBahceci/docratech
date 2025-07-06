<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Spatie Permission cache temizliği
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Super Admin role
        $superAdmin = Role::firstOrCreate(
            ['name' => 'Super Admin', 'guard_name' => 'web'],
            [
                'display_name' => 'Super Admin',
                'description' => 'Full system access with all permissions',
                'type' => 'system',
                'status' => 'active',
                'guard_name' => 'web',
            ]
        );

        // Create Admin role
        $admin = Role::firstOrCreate(
            ['name' => 'Admin', 'guard_name' => 'web'],
            [
                'display_name' => 'Admin',
                'description' => 'Administrative access with most permissions',
                'type' => 'system',
                'status' => 'active',
                'guard_name' => 'web',
            ]
        );

        // Create Manager role
        $manager = Role::firstOrCreate(
            ['name' => 'Manager', 'guard_name' => 'web'],
            [
                'display_name' => 'Manager',
                'description' => 'Management access with limited permissions',
                'type' => 'system',
                'status' => 'active',
                'guard_name' => 'web',
            ]
        );

        // Create User role
        $user = Role::firstOrCreate(
            ['name' => 'User', 'guard_name' => 'web'],
            [
                'display_name' => 'User',
                'description' => 'Basic user access with minimal permissions',
                'type' => 'system',
                'status' => 'active',
                'guard_name' => 'web',
            ]
        );

        // Create Custom Test Role
        $customRole = Role::firstOrCreate(
            ['name' => 'Custom Editor', 'guard_name' => 'web'],
            [
                'display_name' => 'Custom Editor',
                'description' => 'Custom role for testing purposes',
                'type' => 'custom',
                'status' => 'active',
                'guard_name' => 'web',
            ]
        );

        // Assign permissions if they exist
        try {
            // Assign permissions to Super Admin (all permissions)
            if (Permission::count() > 0) {
                $superAdmin->givePermissionTo(Permission::all());
            }

            // Assign permissions to Admin
            $adminPermissions = [
                'users.view', 'users.create', 'users.edit', 'users.export',
                'roles.view', 'roles.create', 'roles.edit', 'roles.assign_permissions',
                'permissions.view',
                'dashboard.view', 'dashboard.stats', 'dashboard.activity', 'dashboard.system_status',
                'activity.view', 'activity.export',
                'logs.view',
                'notifications.view', 'notifications.send',
                'settings.view', 'settings.edit',
                'support.view', 'support.create', 'support.manage',
                'profile.view', 'profile.edit', 'profile.password',
            ];
            $existingAdminPermissions = Permission::whereIn('name', $adminPermissions)->get();
            if ($existingAdminPermissions->count() > 0) {
                $admin->givePermissionTo($existingAdminPermissions);
            }

            // Assign permissions to Manager
            $managerPermissions = [
                'users.view', 'users.create', 'users.edit',
                'roles.view',
                'permissions.view',
                'dashboard.view', 'dashboard.stats', 'dashboard.activity',
                'activity.view',
                'notifications.view', 'notifications.send',
                'settings.view',
                'support.view', 'support.create',
                'profile.view', 'profile.edit', 'profile.password',
            ];
            $existingManagerPermissions = Permission::whereIn('name', $managerPermissions)->get();
            if ($existingManagerPermissions->count() > 0) {
                $manager->givePermissionTo($existingManagerPermissions);
            }

            // Assign permissions to User (Temporarily including management permissions for testing)
            $userPermissions = [
                'dashboard.view',
                'users.view', 'users.create', 'users.edit', // Added for testing
                'roles.view', 'roles.create', 'roles.edit', // Added for testing  
                'permissions.view', // Added for testing
                'activity.view', // Added for testing
                'logs.view', // Added for testing
                'notifications.view',
                'settings.view', // Added for testing
                'support.view', 'support.create',
                'profile.view', 'profile.edit', 'profile.password',
            ];
            $existingUserPermissions = Permission::whereIn('name', $userPermissions)->get();
            if ($existingUserPermissions->count() > 0) {
                $user->givePermissionTo($existingUserPermissions);
            }

            // Assign some permissions to Custom Role
            $customPermissions = [
                'dashboard.view',
                'users.view',
                'roles.view',
                'profile.view', 'profile.edit',
            ];
            $existingCustomPermissions = Permission::whereIn('name', $customPermissions)->get();
            if ($existingCustomPermissions->count() > 0) {
                $customRole->givePermissionTo($existingCustomPermissions);
            }

        } catch (\Exception $e) {
            // Log the error but don't fail the seeder
            \Log::warning('Failed to assign permissions in RoleSeeder: ' . $e->getMessage());
        }

        $this->command->info('Roles seeded successfully!');
    }
}
