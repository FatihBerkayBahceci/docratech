<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Role;

echo "🔍 Role ve User Debug Test\n";
echo "==========================\n\n";

// 1. Rolleri kontrol et
echo "1. Roller:\n";
$roles = Role::all();
foreach ($roles as $role) {
    echo "- ID: {$role->id}, Name: {$role->name}, Display: {$role->display_name}\n";
}

echo "\n2. Kullanıcılar ve Rolleri:\n";
$users = User::with('role')->get();
foreach ($users as $user) {
    $roleName = $user->role ? $user->role->name : 'NO ROLE';
    $roleId = $user->role_id ?: 'NULL';
    echo "- {$user->email}: Role ID: {$roleId}, Role: {$roleName}\n";
}

echo "\n3. Role ID'leri:\n";
$roleIds = User::pluck('role_id')->toArray();
echo "Role IDs: " . implode(', ', $roleIds) . "\n";

echo "\n4. Test tamamlandı!\n"; 