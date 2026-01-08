<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Creating global_approver_configs...\n";
    if (!Schema::hasTable('global_approver_configs')) {
        Schema::create('global_approver_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role_name');
            $table->integer('level');
            $table->timestamps();
        });
        echo "global_approver_configs created.\n";
    } else {
        echo "global_approver_configs already exists.\n";
    }

    echo "Adding use_global_approval to departments...\n";
    if (!Schema::hasColumn('departments', 'use_global_approval')) {
        Schema::table('departments', function (Blueprint $table) {
            $table->boolean('use_global_approval')->default(false);
        });
        echo "Column added.\n";
    } else {
        echo "Column already exists.\n";
    }

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
