<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User Management
            ['name' => 'users.view', 'display_name' => 'View Users', 'key' => 'users.view', 'module' => 'users', 'type' => 'system'],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'key' => 'users.create', 'module' => 'users', 'type' => 'system'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'key' => 'users.edit', 'module' => 'users', 'type' => 'system'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'key' => 'users.delete', 'module' => 'users', 'type' => 'system'],
            ['name' => 'users.export', 'display_name' => 'Export Users', 'key' => 'users.export', 'module' => 'users', 'type' => 'system'],

            // Role Management
            ['name' => 'roles.view', 'display_name' => 'View Roles', 'key' => 'roles.view', 'module' => 'roles', 'type' => 'system'],
            ['name' => 'roles.create', 'display_name' => 'Create Roles', 'key' => 'roles.create', 'module' => 'roles', 'type' => 'system'],
            ['name' => 'roles.edit', 'display_name' => 'Edit Roles', 'key' => 'roles.edit', 'module' => 'roles', 'type' => 'system'],
            ['name' => 'roles.delete', 'display_name' => 'Delete Roles', 'key' => 'roles.delete', 'module' => 'roles', 'type' => 'system'],
            ['name' => 'roles.assign_permissions', 'display_name' => 'Assign Permissions', 'key' => 'roles.assign_permissions', 'module' => 'roles', 'type' => 'system'],

            // Permission Management
            ['name' => 'permissions.view', 'display_name' => 'View Permissions', 'key' => 'permissions.view', 'module' => 'permissions', 'type' => 'system'],
            ['name' => 'permissions.create', 'display_name' => 'Create Permissions', 'key' => 'permissions.create', 'module' => 'permissions', 'type' => 'system'],
            ['name' => 'permissions.edit', 'display_name' => 'Edit Permissions', 'key' => 'permissions.edit', 'module' => 'permissions', 'type' => 'system'],
            ['name' => 'permissions.delete', 'display_name' => 'Delete Permissions', 'key' => 'permissions.delete', 'module' => 'permissions', 'type' => 'system'],

            // Dashboard
            ['name' => 'dashboard.view', 'display_name' => 'View Dashboard', 'key' => 'dashboard.view', 'module' => 'dashboard', 'type' => 'system'],
            ['name' => 'dashboard.stats', 'display_name' => 'View Statistics', 'key' => 'dashboard.stats', 'module' => 'dashboard', 'type' => 'system'],
            ['name' => 'dashboard.activity', 'display_name' => 'View Activity', 'key' => 'dashboard.activity', 'module' => 'dashboard', 'type' => 'system'],
            ['name' => 'dashboard.system_status', 'display_name' => 'View System Status', 'key' => 'dashboard.system_status', 'module' => 'dashboard', 'type' => 'system'],

            // Activity & Logs
            ['name' => 'activity.view', 'display_name' => 'View Activity Logs', 'key' => 'activity.view', 'module' => 'activity', 'type' => 'system'],
            ['name' => 'activity.export', 'display_name' => 'Export Activity Logs', 'key' => 'activity.export', 'module' => 'activity', 'type' => 'system'],
            ['name' => 'logs.view', 'display_name' => 'View System Logs', 'key' => 'logs.view', 'module' => 'logs', 'type' => 'system'],
            ['name' => 'logs.clear', 'display_name' => 'Clear System Logs', 'key' => 'logs.clear', 'module' => 'logs', 'type' => 'system'],

            // Notifications
            ['name' => 'notifications.view', 'display_name' => 'View Notifications', 'key' => 'notifications.view', 'module' => 'notifications', 'type' => 'system'],
            ['name' => 'notifications.send', 'display_name' => 'Send Notifications', 'key' => 'notifications.send', 'module' => 'notifications', 'type' => 'system'],
            ['name' => 'notifications.manage', 'display_name' => 'Manage Notifications', 'key' => 'notifications.manage', 'module' => 'notifications', 'type' => 'system'],

            // Settings
            ['name' => 'settings.view', 'display_name' => 'View Settings', 'key' => 'settings.view', 'module' => 'settings', 'type' => 'system'],
            ['name' => 'settings.edit', 'display_name' => 'Edit Settings', 'key' => 'settings.edit', 'module' => 'settings', 'type' => 'system'],
            ['name' => 'settings.system', 'display_name' => 'System Configuration', 'key' => 'settings.system', 'module' => 'settings', 'type' => 'system'],

            // Support
            ['name' => 'support.view', 'display_name' => 'View Support', 'key' => 'support.view', 'module' => 'support', 'type' => 'system'],
            ['name' => 'support.create', 'display_name' => 'Create Support Tickets', 'key' => 'support.create', 'module' => 'support', 'type' => 'system'],
            ['name' => 'support.manage', 'display_name' => 'Manage Support Tickets', 'key' => 'support.manage', 'module' => 'support', 'type' => 'system'],

            // Profile
            ['name' => 'profile.view', 'display_name' => 'View Profile', 'key' => 'profile.view', 'module' => 'profile', 'type' => 'system'],
            ['name' => 'profile.edit', 'display_name' => 'Edit Profile', 'key' => 'profile.edit', 'module' => 'profile', 'type' => 'system'],
            ['name' => 'profile.password', 'display_name' => 'Change Password', 'key' => 'profile.password', 'module' => 'profile', 'type' => 'system'],

            // Super Admin (Wildcard)
            ['name' => '*', 'display_name' => 'Super Admin', 'key' => '*', 'module' => 'system', 'type' => 'system'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => 'web'],
                [
                    'display_name' => $permission['display_name'],
                    'key' => $permission['key'],
                    'description' => $permission['display_name'] . ' permission',
                    'module' => $permission['module'],
                    'type' => $permission['type'],
                    'status' => 'active',
                ]
            );
        }
        // Spatie Permission cache temizliği
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
