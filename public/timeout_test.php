<?php
// Write to file to bypass any output buffering issues
$output = "Test at " . date('Y-m-d H:i:s') . "\n\n";

$output .= "PDO Drivers: " . implode(", ", PDO::getAvailableDrivers()) . "\n";
$output .= "pdo_pgsql loaded: " . (extension_loaded('pdo_pgsql') ? 'YES' : 'NO') . "\n\n";

try {
    $start = microtime(true);
    $pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=sistem_pr_po', 'postgres', '123456', [
        PDO::ATTR_TIMEOUT => 5,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $elapsed = microtime(true) - $start;
    $output .= "SUCCESS - Connected in " . round($elapsed, 3) . " seconds\n";
    $output .= "Driver: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "\n";
} catch (Exception $e) {
    $elapsed = microtime(true) - $start;
    $output .= "FAILED after " . round($elapsed, 3) . " seconds\n";
    $output .= "Error: " . $e->getMessage() . "\n";
    $output .= "Code: " . $e->getCode() . "\n";
}

file_put_contents(__DIR__ . '/test_output.txt', $output);
echo "Output written to test_output.txt\n";
echo $output;
