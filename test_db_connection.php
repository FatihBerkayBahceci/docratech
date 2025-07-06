<?php
require_once 'vendor/autoload.php';

// Test database connection
try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;port=3306;dbname=docratech',
        'docratech_user',
        '9032229874'
    );
    
    echo "✅ Database connection successful!\n";
    
    // Test if database exists
    $stmt = $pdo->query('SELECT DATABASE() as db_name');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "📊 Connected to database: " . $result['db_name'] . "\n";
    
    // Test if tables exist
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "🔢 Tables found: " . count($tables) . "\n";
    
    if (count($tables) > 0) {
        echo "📋 Tables: " . implode(', ', $tables) . "\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "🔍 Error code: " . $e->getCode() . "\n";
    
    // Test if database exists
    try {
        $pdo_test = new PDO(
            'mysql:host=127.0.0.1;port=3306',
            'root',
            '123456'
        );
        
        echo "✅ MySQL server is accessible with root\n";
        
        // Check if database exists
        $stmt = $pdo_test->query('SHOW DATABASES');
        $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "📊 Available databases: " . implode(', ', $databases) . "\n";
        
        if (!in_array('docratech', $databases)) {
            echo "⚠️  Database 'docratech' does not exist!\n";
        }
        
        // Check if user exists
        $stmt = $pdo_test->query("SELECT User, Host FROM mysql.user WHERE User = 'docratech_user'");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($users)) {
            echo "⚠️  User 'docratech_user' does not exist!\n";
        } else {
            echo "✅ User 'docratech_user' exists\n";
            print_r($users);
        }
        
    } catch (PDOException $e2) {
        echo "❌ Cannot connect to MySQL server: " . $e2->getMessage() . "\n";
    }
} 