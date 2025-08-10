<?php
// Debug file untuk fitur Pengaturan Jam
// Akses melalui: http://yourdomain.com/debug_pengaturan_jam.php

echo "<h1>Debug Fitur Pengaturan Jam</h1>";
echo "<hr>";

// 1. Test koneksi database
echo "<h2>1. Test Koneksi Database</h2>";
try {
    $pdo = new PDO(
        "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_DATABASE'],
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD']
    );
    echo "✅ Database terhubung dengan sukses<br>";
    
    // Test tabel jam_masuks
    $stmt = $pdo->query("SHOW TABLES LIKE 'jam_masuks'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabel jam_masuks ditemukan<br>";
        
        // Cek struktur tabel
        $stmt = $pdo->query("DESCRIBE jam_masuks");
        echo "<strong>Struktur tabel jam_masuks:</strong><br>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- " . $row['Field'] . ": " . $row['Type'] . " " . 
                 ($row['Key'] == 'PRI' ? '(Primary Key)' : '') . 
                 ($row['Null'] == 'NO' ? '(Not Null)' : '') . "<br>";
        }
        
        // Cek jumlah data
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM jam_masuks");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "📊 Total data di tabel: " . $count . "<br>";
        
        if ($count > 0) {
            $stmt = $pdo->query("SELECT * FROM jam_masuks ORDER BY created_at DESC LIMIT 5");
            echo "<strong>Data terbaru:</strong><br>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "- ID: " . $row['id'] . ", Start: " . $row['start_time'] . ", End: " . $row['end_time'] . ", " .
                     "Pulang: " . $row['jam_pulang_minimal'] . ", Selisih: " . $row['selisih_jam_minimal'] . "<br>";
            }
        }
    } else {
        echo "❌ Tabel jam_masuks TIDAK ditemukan<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Error database: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// 2. Test environment variables
echo "<h2>2. Environment Variables</h2>";
$env_vars = [
    'DB_HOST',
    'DB_DATABASE',
    'DB_USERNAME',
    'DB_PASSWORD',
    'APP_URL'
];

foreach ($env_vars as $var) {
    $value = $_ENV[$var] ?? 'TIDAK DI SET';
    if ($var == 'DB_PASSWORD' && $value != 'TIDAK DI SET') {
        $value = str_repeat('*', strlen($value));
    }
    echo "<strong>" . $var . ":</strong> " . $value . "<br>";
}

echo "<hr>";

// 3. Test form submission
echo "<h2>3. Test Form Submission</h2>";
echo "<form method='POST' action='../admin/libur/updateJamMasuk'>";
echo "<input type='hidden' name='_token' value='test'>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label>Start Time: <input type='time' name='start_time' value='07:00' required></label>";
echo "</div>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label>End Time: <input type='time' name='end_time' value='08:30' required></label>";
echo "</div>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label>Jam Pulang Minimal: <input type='time' name='jam_pulang_minimal' value='12:00' required></label>";
echo "</div>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label>Selisih Jam Minimal: <input type='number' name='selisih_jam_minimal' value='4' min='1' max='12' required></label>";
echo "</div>";
echo "<button type='submit'>Test Update Jam</button>";
echo "</form>";

echo "<hr>";

// 4. Test validasi
echo "<h2>4. Test Validasi</h2>";
$test_cases = [
    [
        'start_time' => '07:00',
        'end_time' => '08:30',
        'jam_pulang_minimal' => '12:00',
        'selisih_jam_minimal' => '4',
        'expected' => 'Valid'
    ],
    [
        'start_time' => '08:30',
        'end_time' => '07:00',
        'jam_pulang_minimal' => '12:00',
        'selisih_jam_minimal' => '4',
        'expected' => 'Invalid: end_time < start_time'
    ],
    [
        'start_time' => '07:00',
        'end_time' => '08:30',
        'jam_pulang_minimal' => '08:00',
        'selisih_jam_minimal' => '4',
        'expected' => 'Invalid: jam_pulang_minimal < end_time'
    ]
];

foreach ($test_cases as $i => $test) {
    echo "<strong>Test Case " . ($i+1) . ":</strong> " . $test['expected'] . "<br>";
    echo "- Start: " . $test['start_time'] . ", End: " . $test['end_time'] . ", " .
         "Pulang: " . $test['jam_pulang_minimal'] . ", Selisih: " . $test['selisih_jam_minimal'] . "<br>";
    
    // Validasi manual
    $errors = [];
    if ($test['start_time'] >= $test['end_time']) {
        $errors[] = 'Jam tutup harus setelah jam mulai';
    }
    if ($test['end_time'] >= $test['jam_pulang_minimal']) {
        $errors[] = 'Jam pulang minimal harus setelah jam tutup masuk';
    }
    
    if (empty($errors)) {
        echo "✅ Validasi: PASS<br>";
    } else {
        echo "❌ Validasi: FAIL - " . implode(', ', $errors) . "<br>";
    }
    echo "<br>";
}

echo "<hr>";

// 5. Rekomendasi
echo "<h2>5. Rekomendasi Perbaikan</h2>";
echo "<ol>";
echo "<li><strong>Database:</strong> Pastikan tabel jam_masuks ada dan struktur benar</li>";
echo "<li><strong>Model:</strong> Pastikan model JamMasuk dapat diakses</li>";
echo "<li><strong>Route:</strong> Pastikan route admin.libur.updateJamMasuk terdaftar</li>";
echo "<li><strong>Middleware:</strong> Pastikan user sudah login dan punya akses admin</li>";
echo "<li><strong>CSRF:</strong> Pastikan token CSRF valid</li>";
echo "<li><strong>Validation:</strong> Periksa apakah ada error validasi yang tidak ditampilkan</li>";
echo "<li><strong>Session:</strong> Pastikan session berfungsi dengan baik</li>";
echo "</ol>";

echo "<hr>";
echo "<p><strong>Catatan:</strong> File ini hanya untuk debugging. Hapus setelah selesai troubleshooting.</p>";
?>
