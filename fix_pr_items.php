<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Creating pr_items table...\n";

    if (!Schema::hasTable('pr_items')) {
        Schema::create('pr_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained('purchase_requests')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products'); 
            $table->string('item_name')->nullable();
            $table->integer('quantity');
            $table->string('unit')->nullable();
            $table->decimal('price_estimation', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });
        echo "pr_items table created successfully.\n";
    } else {
        echo "pr_items table already exists.\n";
    }

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
