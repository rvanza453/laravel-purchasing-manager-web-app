<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\WarehouseStock;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportWarehouseStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:warehouse-stock {file} {warehouse_id=7}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import stock from CSV to a specific warehouse';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');
        $warehouseId = $this->argument('warehouse_id');

        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return 1;
        }

        $this->info("Starting import for Warehouse ID: $warehouseId from file: $file");

        $handle = fopen($file, 'r');
        $header = fgetcsv($handle); 
        
        // Detect Column Mapping
        // Check if first column is empty (Original CSV format) or ITEM ID (KDE CSV format)
        $firstCol = isset($header[0]) ? strtoupper(trim($header[0])) : '';
        // Remove BOM if present
        $firstCol = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $firstCol);
        
        $useZeroIndex = false;
        if (str_contains($firstCol, 'ITEM ID')) {
            $useZeroIndex = true;
            $this->info("Detected Format: Standard CSV (0-indexed)");
        } else {
            $this->info("Detected Format: Offset CSV (1-indexed)");
        }

        $idxCode = $useZeroIndex ? 0 : 1;
        $idxName = $useZeroIndex ? 1 : 2;
        $idxUnit = $useZeroIndex ? 2 : 3;
        $idxQty = $useZeroIndex ? 3 : 4;
        $idxPrice = $useZeroIndex ? 4 : 5;

        $count = 0;
        $errors = 0;

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                
                $code = trim($row[$idxCode] ?? '');
                if (empty($code)) {
                    continue; // Skip empty rows
                }

                $name = trim($row[$idxName] ?? '');
                $unit = trim($row[$idxUnit] ?? '');
                
                // Sanitize QTY
                $qtyRaw = $row[$idxQty] ?? 0;
                $qty = $this->sanitizeNumber($qtyRaw, true); // True implies convert to integer

                // Sanitize Price
                $priceRaw = $row[$idxPrice] ?? 0;
                $price = $this->sanitizeNumber($priceRaw, false);

                try {
                    // Update or Create Product
                    // We use updateOrCreate to ensure we catch existing items by code
                    $product = Product::updateOrCreate(
                        ['code' => $code],
                        [
                            'name' => $name,
                            'unit' => $unit,
                            'price_estimation' => $price,
                            // Set defaults for other required fields if they don't exist
                            'category' => $product->category ?? 'General', 
                            'min_stock' => $product->min_stock ?? 0,
                        ]
                    );

                    // Update Warehouse Stock
                    WarehouseStock::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'warehouse_id' => $warehouseId,
                        ],
                        [
                            'quantity' => $qty,
                        ]
                    );

                    $count++;
                    if ($count % 100 == 0) {
                        $this->info("Processed $count records...");
                    }

                } catch (\Exception $e) {
                    $this->error("Error processing row for code $code: " . $e->getMessage());
                    $errors++;
                }
            }

            DB::commit();
            $this->info("Import completed successfully! Format Used: " . ($useZeroIndex ? "Standard" : "Offset"));
            $this->info("Total processed: $count");
            $this->info("Total errors: $errors");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Fatal error during import: " . $e->getMessage());
            return 1;
        } finally {
            fclose($handle);
        }

        return 0;
    }

    private function sanitizeNumber($value, $isInteger = false)
    {
        if (is_numeric($value)) {
            return $isInteger ? (int)round($value) : $value;
        }

        // Handle "NaN" or "Infinity"
        if (strcasecmp($value, 'NaN') === 0 || strcasecmp($value, 'Infinity') === 0) {
            return 0;
        }

        // Remove commas (thousands separator)
        $clean = str_replace(',', '', $value);
        
        // Remove quotes if any
        $clean = str_replace(['"', "'"], '', $clean);

        if (is_numeric($clean)) {
            return $isInteger ? (int)round($clean) : $clean;
        }

        return 0;
    }
}
