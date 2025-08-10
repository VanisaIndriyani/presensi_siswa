<?php
/**
 * Debug File untuk Kepala Sekolah Dashboard di Hosting
 * File ini dibuat untuk mengatasi masalah 404 error di hosting
 * Akses file ini langsung dari browser: bitubi.my.id/presensi_siswa/public/debug_hosting_kepala_sekolah.php
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Debug Dashboard Kepala Sekolah - Hosting</h1>";
echo "<p><strong>Waktu:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<hr>";

// 1. Test Database Connection
echo "<h2>1. 🗄️ Test Koneksi Database</h2>";
try {
    // Load Laravel environment
    require_once 'vendor/autoload.php';
    
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Test database connection
    $pdo = DB::connection()->getPdo();
    echo "✅ Database berhasil terkoneksi<br>";
    echo "Database: " . $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "<br>";
    
    // Test basic queries
    $totalSiswa = DB::table('siswas')->count();
    echo "Total Siswa: " . $totalSiswa . "<br>";
    
    $totalPresensi = DB::table('presensis')->count();
    echo "Total Presensi: " . $totalPresensi . "<br>";
    
} catch (Exception $e) {
    echo "❌ Error Database: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// 2. Test Models
echo "<h2>2. 🏗️ Test Models</h2>";
try {
    // Test Siswa model
    $siswa = new \App\Models\Siswa();
    echo "✅ Model Siswa berhasil dibuat<br>";
    
    // Test Presensi model
    $presensi = new \App\Models\Presensi();
    echo "✅ Model Presensi berhasil dibuat<br>";
    
    // Test User model
    $user = new \App\Models\User();
    echo "✅ Model User berhasil dibuat<br>";
    
} catch (Exception $e) {
    echo "❌ Error Model: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// 3. Test Controller Methods
echo "<h2>3. 🎮 Test Controller Methods</h2>";
try {
    $controller = new \App\Http\Controllers\KepalaSekolah\DashboardController();
    echo "✅ Controller berhasil dibuat<br>";
    
    // Test getSiswaAlpa method
    $siswaAlpa = $controller->getSiswaAlpa();
    echo "✅ Method getSiswaAlpa berhasil dijalankan<br>";
    
    // Test getSiswaByStatus method
    $siswaByStatus = $controller->getSiswaByStatus('tepat_waktu');
    echo "✅ Method getSiswaByStatus berhasil dijalankan<br>";
    
} catch (Exception $e) {
    echo "❌ Error Controller: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// 4. Test Routes
echo "<h2>4. 🛣️ Test Routes</h2>";
try {
    $router = app('router');
    $routes = $router->getRoutes();
    
    $kepalaSekolahRoutes = [];
    foreach ($routes as $route) {
        if (strpos($route->uri(), 'kepala-sekolah') !== false) {
            $kepalaSekolahRoutes[] = $route->uri() . ' (' . implode('|', $route->methods()) . ')';
        }
    }
    
    echo "✅ Routes Kepala Sekolah ditemukan:<br>";
    foreach ($kepalaSekolahRoutes as $route) {
        echo "&nbsp;&nbsp;• " . $route . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error Routes: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// 5. Test API Endpoints
echo "<h2>5. 🌐 Test API Endpoints</h2>";
echo "<p>Klik link berikut untuk test endpoint:</p>";

$baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
$baseUrl = rtrim($baseUrl, '/');

echo "<ul>";
echo "<li><a href='{$baseUrl}/kepala-sekolah/siswa-alpa' target='_blank'>Test: {$baseUrl}/kepala-sekolah/siswa-alpa</a></li>";
echo "<li><a href='{$baseUrl}/kepala-sekolah/siswa-by-status/tepat_waktu' target='_blank'>Test: {$baseUrl}/kepala-sekolah/siswa-by-status/tepat_waktu</a></li>";
echo "<li><a href='{$baseUrl}/kepala-sekolah/api/siswa-alpa' target='_blank'>Test: {$baseUrl}/kepala-sekolah/api/siswa-alpa</a></li>";
echo "<li><a href='{$baseUrl}/kepala-sekolah/api/siswa-by-status/tepat_waktu' target='_blank'>Test: {$baseUrl}/kepala-sekolah/api/siswa-by-status/tepat_waktu</a></li>";
echo "</ul>";

echo "<hr>";

// 6. Environment Info
echo "<h2>6. ⚙️ Environment Info</h2>";
echo "<strong>APP_ENV:</strong> " . (env('APP_ENV') ?: 'Not set') . "<br>";
echo "<strong>APP_DEBUG:</strong> " . (env('APP_DEBUG') ?: 'Not set') . "<br>";
echo "<strong>APP_URL:</strong> " . (env('APP_URL') ?: 'Not set') . "<br>";
echo "<strong>Current URL:</strong> " . $_SERVER['REQUEST_URI'] . "<br>";
echo "<strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "<br>";

echo "<hr>";

// 7. Manual Test Links
echo "<h2>7. 🔗 Manual Test Links</h2>";
echo "<p>Test manual dengan mengklik panel di dashboard:</p>";
echo "<a href='{$baseUrl}/kepala-sekolah' class='btn btn-primary' target='_blank'>Buka Dashboard Kepala Sekolah</a>";

echo "<hr>";
echo "<p><strong>Catatan:</strong> Jika masih ada error, periksa:</p>";
echo "<ul>";
echo "<li>File .htaccess di folder public</li>";
echo "<li>Konfigurasi virtual host di hosting</li>";
echo "<li>Permission folder storage dan bootstrap/cache</li>";
echo "<li>Log error di storage/logs/laravel.log</li>";
echo "</ul>";

echo "<p><em>File debug ini dibuat untuk mengatasi masalah hosting. Hapus file ini setelah masalah teratasi.</em></p>";
?>
