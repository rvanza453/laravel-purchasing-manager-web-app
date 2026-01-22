<?php
try {
    $pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=sistem_pr_po', 'postgres', '123456');
    echo "CLI PDO Connection: SUCCESS\n";
    echo "Driver: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "\n";
} catch (Exception $e) {
    echo "CLI PDO Connection: FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
}
