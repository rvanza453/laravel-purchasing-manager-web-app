<?php
header('Content-Type: text/plain');
echo date('Y-m-d H:i:s') . " - Test " . rand(1000, 9999) . "\n\n";

echo "PDO Drivers: " . implode(", ", PDO::getAvailableDrivers()) . "\n";
echo "pdo_pgsql: " . (extension_loaded('pdo_pgsql') ? 'loaded' : 'NOT loaded') . "\n\n";

try {
    $pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=sistem_pr_po', 'postgres', '123456');
    echo "Web PDO Connection: SUCCESS\n";
} catch (Exception $e) {
    echo "Web PDO Connection: FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
}
