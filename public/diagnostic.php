<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "START DIAGNOSTIC\n\n";
flush();

echo "1. PDO Drivers: ";
try {
    echo implode(", ", PDO::getAvailableDrivers());
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
echo "\n\n";
flush();

echo "2. pdo_pgsql loaded: " . (extension_loaded('pdo_pgsql') ? 'YES' : 'NO') . "\n";
echo "3. pgsql loaded: " . (extension_loaded('pgsql') ? 'YES' : 'NO') . "\n\n";
flush();

echo "4. Direct PDO test:\n";
try {
    $pdo = new PDO("pgsql:host=127.0.0.1;port=5432;dbname=sistem_pr_po", "postgres", "123456");
    echo "   SUCCESS - Connected to PostgreSQL\n\n";
} catch (Exception $e) {
    echo "   FAILED: " . $e->getMessage() . "\n\n";
}
flush();

echo "5. Loading Laravel...\n";
try {
    require __DIR__ . '/../vendor/autoload.php';
    echo "   Autoload OK\n";
    flush();
    
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    echo "   App OK\n";
    flush();
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "   Kernel OK\n";
    flush();
    
    $kernel->bootstrap();
    echo "   Bootstrap OK\n\n";
    flush();
    
    echo "6. Laravel Config:\n";
    echo "   DB Default: " . config('database.default') . "\n";
    echo "   DB Driver: " . config('database.connections.pgsql.driver') . "\n\n";
    flush();
    
    echo "7. Laravel DB Connection:\n";
    $pdo = DB::connection()->getPdo();
    echo "   SUCCESS - Driver: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "\n";
    
} catch (Exception $e) {
    echo "   FAILED: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\nEND DIAGNOSTIC";
