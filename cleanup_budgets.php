<?php

// Clean up zero-amount budgets
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$deleted = DB::table('budgets')->where('amount', 0)->delete();

echo "Deleted {$deleted} zero-amount budget records.\n";
