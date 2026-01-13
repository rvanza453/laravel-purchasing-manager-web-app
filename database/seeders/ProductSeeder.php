<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Lokasi File CSV
        $csvFile = database_path('seeders/data/data_barang.csv');

        // 2. Buka File
        $fileHandle = fopen($csvFile, 'r');

        // Lewati baris header (jika ada judul kolom di baris 1)
        fgetcsv($fileHandle); 

        // 3. Loop data
        while (($line = fgets($fileHandle)) !== FALSE) {
            $line = trim($line);
            if (empty($line)) continue;

            // Attempt to parse CSV without treating " as enclosure if possible, 
            // or just use loose parsing since the data contains unescaped quotes like 6"
            // We use a rare character as enclosure to prevent " from swallowing lines
            $row = str_getcsv($line, ',', '~'); 

            // Fallback: if standard parse fails or column count is wrong, try strict explode if we assume no commas in data
            if (count($row) < 5) {
                 $row = explode(',', $line);
            }

            if (count($row) < 2) continue;

            $code = $row[1] ?? '';
            $exists = DB::table('products')->where('code', $code)->exists();

            $data = [
                'name'       => str_replace('"', '', $row[2] ?? 'Unknown'), // Clean cleanup
                'unit'       => $row[3] ?? '',
                'category'   => $row[5] ?? '', // Assuming category is index 5 based on user code
                'price_estimation' => isset($row[4]) ? (float)str_replace(['.', ','], ['', '.'], $row[4]) : 0, 
                'updated_at' => now(),
            ];

            if ($exists) {
                DB::table('products')->where('code', $code)->update($data);
            } else {
                $data['code'] = $code;
                $data['created_at'] = now();
                DB::table('products')->insert($data);
            }
        }

        fclose($fileHandle);
    }
}
