<?php
set_time_limit(15);
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/plain');

echo "Laravel Connection Test - " . date('H:i:s') . "\n\n";

try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();
    
    echo "Laravel bootstrapped successfully\n";
    echo "DB Connection: " . config('database.default') . "\n";
    echo "DB Host: " . config('database.connections.pgsql.host') . "\n";
    echo "DB Database: " . config('database.connections.pgsql.database') . "\n\n";
    
    echo "Attempting to get PDO...\n";
    $pdo = DB::connection()->getPdo();
    
    echo "SUCCESS! Connected to PostgreSQL via Laravel\n";
    echo "Driver: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "\n";
    
    echo "\nTesting query...\n";
    $result = DB::select('SELECT VERSION()');
    echo "Query result: " . print_r($result, true) . "\n";
    
} catch (Exception $e) {
    echo "FAILED!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
