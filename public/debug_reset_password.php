<?php
// Debug file untuk fitur Reset Password
// Akses melalui: http://yourdomain.com/debug_reset_password.php

echo "<h1>Debug Fitur Reset Password</h1>";
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
    
    // Test tabel password_resets
    $stmt = $pdo->query("SHOW TABLES LIKE 'password_resets'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabel password_resets ditemukan<br>";
        
        // Cek struktur tabel
        $stmt = $pdo->query("DESCRIBE password_resets");
        echo "<strong>Struktur tabel password_resets:</strong><br>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- {$row['Field']}: {$row['Type']} " . 
                 ($row['Key'] == 'PRI' ? '(Primary Key)' : '') . 
                 ($row['Null'] == 'NO' ? '(Not Null)' : '') . "<br>";
        }
        
        // Cek jumlah data
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM password_resets");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "📊 Total data di tabel: {$count}<br>";
        
        if ($count > 0) {
            $stmt = $pdo->query("SELECT * FROM password_resets ORDER BY created_at DESC LIMIT 5");
            echo "<strong>Data terbaru:</strong><br>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "- Email: {$row['email']}, Token: " . substr($row['token'], 0, 20) . "...<br>";
            }
        }
    } else {
        echo "❌ Tabel password_resets TIDAK ditemukan<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Error database: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// 2. Test environment variables
echo "<h2>2. Environment Variables</h2>";
$env_vars = [
    'MAIL_MAILER',
    'MAIL_HOST',
    'MAIL_PORT',
    'MAIL_USERNAME',
    'MAIL_PASSWORD',
    'MAIL_ENCRYPTION',
    'MAIL_FROM_ADDRESS',
    'MAIL_FROM_NAME',
    'APP_URL'
];

foreach ($env_vars as $var) {
    $value = $_ENV[$var] ?? 'TIDAK DI SET';
    if ($var == 'MAIL_PASSWORD' && $value != 'TIDAK DI SET') {
        $value = str_repeat('*', strlen($value));
    }
    echo "<strong>{$var}:</strong> {$value}<br>";
}

echo "<hr>";

// 3. Test Laravel Mail Configuration
echo "<h2>3. Test Laravel Mail</h2>";
if (file_exists('../vendor/autoload.php')) {
    require_once '../vendor/autoload.php';
    
    try {
        // Load Laravel app
        $app = require_once '../bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        
        echo "✅ Laravel app berhasil di-bootstrap<br>";
        
        // Test mail configuration
        $mailConfig = config('mail');
        echo "<strong>Mail Default:</strong> " . $mailConfig['default'] . "<br>";
        echo "<strong>Mail Host:</strong> " . $mailConfig['mailers']['smtp']['host'] . "<br>";
        echo "<strong>Mail Port:</strong> " . $mailConfig['mailers']['smtp']['port'] . "<br>";
        
        // Test jika bisa membuat token
        $token = \Illuminate\Support\Str::random(60);
        echo "✅ Token berhasil dibuat: " . substr($token, 0, 20) . "...<br>";
        
        // Test hash password
        $hashedPassword = password_hash('test123', PASSWORD_DEFAULT);
        echo "✅ Password hash berhasil dibuat<br>";
        
    } catch (Exception $e) {
        echo "❌ Error Laravel: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Vendor autoload tidak ditemukan<br>";
}

echo "<hr>";

// 4. Test API endpoints
echo "<h2>4. Test API Endpoints</h2>";
$base_url = $_ENV['APP_URL'] ?? 'http://localhost';
$endpoints = [
    '/password/email',
    '/password/reset',
    '/password/update'
];

foreach ($endpoints as $endpoint) {
    $url = $base_url . $endpoint;
    echo "<strong>Testing:</strong> <a href='{$url}' target='_blank'>{$url}</a><br>";
}

echo "<hr>";

// 5. Manual test form
echo "<h2>5. Manual Test Reset Password</h2>";
echo "<form method='POST' action='../password/email'>";
echo "<input type='hidden' name='_token' value='" . csrf_token() . "'>";
echo "<label>Email: <input type='email' name='email' required></label><br><br>";
echo "<button type='submit'>Test Reset Password</button>";
echo "</form>";

echo "<hr>";

// 6. Test cURL jika tersedia
echo "<h2>6. Test cURL (jika tersedia)</h2>";
if (function_exists('curl_init')) {
    echo "✅ cURL tersedia<br>";
    
    $test_url = $base_url . '/password/email';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $test_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ cURL Error: {$error}<br>";
    } else {
        echo "✅ cURL Response: HTTP {$http_code}<br>";
        if ($http_code == 200) {
            echo "✅ Endpoint dapat diakses<br>";
        } else {
            echo "❌ Endpoint error dengan kode {$http_code}<br>";
        }
    }
} else {
    echo "❌ cURL tidak tersedia<br>";
}

echo "<hr>";

// 7. Rekomendasi
echo "<h2>7. Rekomendasi Perbaikan</h2>";
echo "<ol>";
echo "<li><strong>Konfigurasi Email:</strong> Ubah MAIL_MAILER dari 'log' ke 'smtp' di file .env</li>";
echo "<li><strong>SMTP Settings:</strong> Isi MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD</li>";
echo "<li><strong>APP_URL:</strong> Pastikan APP_URL sesuai dengan domain hosting</li>";
echo "<li><strong>Database:</strong> Pastikan tabel password_resets ada dan struktur benar</li>";
echo "<li><strong>Cache:</strong> Jalankan 'php artisan config:cache' setelah ubah .env</li>";
echo "</ol>";

echo "<hr>";
echo "<p><strong>Catatan:</strong> File ini hanya untuk debugging. Hapus setelah selesai troubleshooting.</p>";
?>
