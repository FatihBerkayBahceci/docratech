<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class FixUserRoles extends Command
{
    protected $signature = 'users:fix-roles';
    protected $description = 'Fix user roles by assigning role_id and Spatie roles';

    public function handle()
    {
        $this->info('Fixing user roles...');

        $role = Role::first();
        if (!$role) {
            $this->error('No roles found in database!');
            return 1;
        }

        $this->info("Using role: {$role->name} (ID: {$role->id})");

        $users = User::all();
        $updated = 0;

        foreach ($users as $user) {
            // Set role_id
            $user->role_id = $role->id;
            $user->save();
            
            // Assign Spatie role
            $user->assignRole($role->name);
            
            $this->line("Updated user: {$user->email} -> Role: {$role->name}");
            $updated++;
        }

        $this->info("Updated {$updated} users with role: {$role->name}");

        // Verify
        $usersWithRoles = User::with('role')->get();
        foreach ($usersWithRoles as $user) {
            $roleName = $user->role ? $user->role->name : 'NULL';
            $this->line("User: {$user->email} -> Role ID: {$user->role_id} -> Role: {$roleName}");
        }

        return 0;
    }
} 