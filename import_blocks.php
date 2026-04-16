<?php
// 1. Konfigurasi Database
$host = 'localhost';
$dbname = 'temp'; // Ganti dengan nama database Anda jika berbeda
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Mapping Manual AFDELING ke ID Sub Department
    // PASTIKAN ID INI BENAR-BENAR ADA DI TABEL `sub_departments`
    $subDeptLookup = [
        'AF01' => 48,
        'AF02' => 49,
        'AF03' => 50,
        'AF04' => 51,
        'AF05' => 52
    ];

    // 3. Buka File CSV
    $csvFile = 'master_block_sae.csv'; 
    $file = fopen($csvFile, 'r');
    if ($file === false) {
        die("Gagal: Tidak dapat membaca file CSV.");
    }

    // Lewati baris pertama (Header) - PERBAIKAN DEPRECATED fgetcsv
    fgetcsv($file, 0, ",", "\"", "\\"); 

    // 4. Siapkan Query Insert
    // (Pastikan kolom area, qty_seed, growing_year, plasma sudah dibuat di database)
    $insertQuery = "INSERT INTO blocks (name, sub_department_id, area, qty_seed, growing_year, plasma, created_at)
                    VALUES (:name, :sub_department_id, :area, :qty_seed, :growing_year, :plasma, NOW())";
    $insertStmt = $pdo->prepare($insertQuery);

    $berhasil = 0;
    $rowNum = 2; // Mulai dari baris ke-2 (karena baris 1 header)

    // 5. Looping isi CSV - PERBAIKAN DEPRECATED fgetcsv
    while (($data = fgetcsv($file, 1000, ",", "\"", "\\")) !== false) {
        // Abaikan baris kosong
        if (empty(array_filter($data))) continue;

        $blockName   = trim($data[0]); 
        $afdelingCoa = trim($data[1]); 
        $area        = !empty($data[2]) ? $data[2] : null;
        $qtySeed     = !empty($data[3]) ? $data[3] : null;
        $growingYear = !empty($data[4]) ? $data[4] : null;
        $plasma      = !empty($data[5]) ? $data[5] : null;

        // 6. Pencocokan AFDELING (CSV)
        if (isset($subDeptLookup[$afdelingCoa])) {
            $subDepartmentId = $subDeptLookup[$afdelingCoa];

            // Eksekusi Insert
            $insertStmt->execute([
                ':name'              => $blockName,
                ':sub_department_id' => $subDepartmentId,
                ':area'              => $area,
                ':qty_seed'          => $qtySeed,
                ':growing_year'      => $growingYear,
                ':plasma'            => $plasma
            ]);
            $berhasil++;
        } else {
            echo "Peringatan: Baris $rowNum dilewati. AFDELING '$afdelingCoa' tidak ditemukan di Mapping.<br>";
        }
        $rowNum++;
    }

    fclose($file);
    echo "<h2>Proses Selesai!</h2>";
    echo "Total data berhasil diimpor: <strong>$berhasil</strong> baris.";

} catch (PDOException $e) {
    echo "<b>Terjadi Error Sistem:</b> " . $e->getMessage();
}
?>