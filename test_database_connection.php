<?php

/**
 * DocraTech Database Connection Test
 * This script tests the MySQL connection and verifies the application setup
 */

require_once 'vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;

echo "🏥 DocraTech Database Connection Test\n";
echo "=====================================\n\n";

// 1. Test database connection
echo "1. Testing MySQL Connection:\n";
echo "----------------------------\n";
try {
    DB::connection()->getPdo();
    echo "✅ MySQL connection: SUCCESS\n";
    
    $dbName = DB::connection()->getDatabaseName();
    echo "✅ Connected to database: {$dbName}\n";
    
} catch (Exception $e) {
    echo "❌ MySQL connection: FAILED\n";
    echo "   Error: " . $e->getMessage() . "\n";
    echo "\n❌ Please check your .env database settings!\n";
    exit(1);
}

// 2. Test table existence
echo "\n2. Testing Table Structure:\n";
echo "---------------------------\n";
$tables = ['users', 'roles', 'permissions', 'personal_access_tokens', 'sessions'];

foreach ($tables as $table) {
    try {
        $count = DB::table($table)->count();
        echo "✅ Table '{$table}': {$count} records\n";
    } catch (Exception $e) {
        echo "❌ Table '{$table}': MISSING or ERROR\n";
        echo "   Error: " . $e->getMessage() . "\n";
    }
}

// 3. Test authentication data
echo "\n3. Testing Authentication Data:\n";
echo "-------------------------------\n";
try {
    $userCount = User::count();
    $roleCount = Role::count();
    
    echo "✅ Users in database: {$userCount}\n";
    echo "✅ Roles in database: {$roleCount}\n";
    
    if ($userCount > 0) {
        $testUser = User::first();
        echo "✅ Test user: {$testUser->email}\n";
        echo "✅ User has role: " . ($testUser->role ? $testUser->role->name : 'NO ROLE') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Authentication test: FAILED\n";
    echo "   Error: " . $e->getMessage() . "\n";
}

// 4. Test Laravel Sanctum
echo "\n4. Testing Laravel Sanctum:\n";
echo "---------------------------\n";
try {
    $tokenCount = DB::table('personal_access_tokens')->count();
    echo "✅ Sanctum tokens table: {$tokenCount} tokens\n";
    
} catch (Exception $e) {
    echo "❌ Sanctum test: FAILED\n";
    echo "   Error: " . $e->getMessage() . "\n";
}

// 5. Environment check
echo "\n5. Environment Configuration:\n";
echo "-----------------------------\n";
echo "✅ App Environment: " . config('app.env') . "\n";
echo "✅ Database Driver: " . config('database.default') . "\n";
echo "✅ Database Host: " . config('database.connections.mysql.host') . "\n";
echo "✅ Database Name: " . config('database.connections.mysql.database') . "\n";
echo "✅ Cache Driver: " . config('cache.default') . "\n";
echo "✅ Session Driver: " . config('session.driver') . "\n";

echo "\n🏁 Test Complete!\n";
echo "==================\n";

if (DB::connection()->getPdo() && User::count() > 0) {
    echo "✅ Your DocRaTech application is ready!\n";
    echo "✅ You can now start the development server:\n";
    echo "   php artisan serve\n";
    echo "   npm run dev\n";
} else {
    echo "❌ Setup incomplete. Please run:\n";
    echo "   php artisan migrate\n";
    echo "   php artisan db:seed\n";
}

echo "\n"; 