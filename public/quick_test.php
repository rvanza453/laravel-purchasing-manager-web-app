<?php
set_time_limit(10);
header('Content-Type: text/plain');

echo "Quick Test - " . date('H:i:s') . "\n\n";

// Test 1: localhost
echo "Test 1: Connecting to localhost...\n";
try {
    $pdo = new PDO('pgsql:host=localhost;port=5432;dbname=sistem_pr_po', 'postgres', '123456', [
        PDO::ATTR_TIMEOUT => 3
    ]);
    echo "SUCCESS with localhost\n\n";
} catch (Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n\n";
}

// Test 2: 127.0.0.1
echo "Test 2: Connecting to 127.0.0.1...\n";
try {
    $pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=sistem_pr_po', 'postgres', '123456', [
        PDO::ATTR_TIMEOUT => 3
    ]);
    echo "SUCCESS with 127.0.0.1\n\n";
} catch (Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n\n";
}

echo "Done.";
