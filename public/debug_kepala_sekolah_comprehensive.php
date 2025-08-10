<?php
// Debug file untuk Kepala Sekolah Dashboard - Comprehensive Testing
// Letakkan file ini di folder public dan akses via browser

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Debug Comprehensive Kepala Sekolah Dashboard</h1>";
echo "<p><strong>Waktu:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<hr>";

// 1. Test Database Connection
echo "<h2>1. Database Connection Test</h2>";
try {
    $pdo = new PDO(
        "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_DATABASE'],
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD']
    );
    echo "✅ Database connection successful<br>";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM siswa");
    $result = $stmt->fetch();
    echo "✅ Total siswa: " . $result['total'] . "<br>";
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// 2. Test Environment Variables
echo "<h2>2. Environment Variables Test</h2>";
$envVars = [
    'APP_ENV',
    'APP_DEBUG',
    'APP_URL',
    'DB_HOST',
    'DB_DATABASE',
    'DB_USERNAME',
    'DB_PASSWORD'
];

foreach ($envVars as $var) {
    $value = $_ENV[$var] ?? 'NOT SET';
    $status = $_ENV[$var] ? '✅' : '❌';
    echo "$status $var: $value<br>";
}

echo "<hr>";

// 3. Test Laravel Models
echo "<h2>3. Laravel Models Test</h2>";

// Load Laravel
require_once '../vendor/autoload.php';
require_once '../bootstrap/app.php';

try {
    $app = require_once '../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "✅ Laravel bootstrapped successfully<br>";
    
    // Test Siswa model
    $siswaCount = \App\Models\Siswa::count();
    echo "✅ Siswa model working, total: $siswaCount<br>";
    
    // Test Presensi model
    $presensiCount = \App\Models\Presensi::count();
    echo "✅ Presensi model working, total: $presensiCount<br>";
    
    // Test Guru model
    $guruCount = \App\Models\Guru::count();
    echo "✅ Guru model working, total: $guruCount<br>";
    
} catch (Exception $e) {
    echo "❌ Laravel bootstrap failed: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// 4. Test API Endpoints
echo "<h2>4. API Endpoints Test</h2>";

$baseUrl = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$baseUrl = str_replace('/debug_kepala_sekolah_comprehensive.php', '', $baseUrl);

$endpoints = [
    '/kepala-sekolah/siswa-alpa',
    '/kepala-sekolah/api/siswa-alpa',
    '/kepala-sekolah/siswa-by-status/total',
    '/kepala-sekolah/api/siswa-by-status/total'
];

foreach ($endpoints as $endpoint) {
    $url = "http://$baseUrl$endpoint";
    echo "<p><strong>Testing:</strong> $url</p>";
    
    // Create manual test link
    echo "<a href='$url' target='_blank'>🔗 Test $endpoint</a><br>";
    
    // Test with cURL if available
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            echo "❌ cURL Error: $error<br>";
        } else {
            echo "✅ HTTP Code: $httpCode<br>";
            if ($httpCode === 200) {
                echo "✅ Response received<br>";
                if (strlen($response) > 100) {
                    echo "✅ Response length: " . strlen($response) . " characters<br>";
                }
            } else {
                echo "❌ Unexpected HTTP code<br>";
            }
        }
    } else {
        echo "⚠️ cURL not available<br>";
    }
    echo "<br>";
}

echo "<hr>";

// 5. Test Route List
echo "<h2>5. Route List Test</h2>";
echo "<p>Testing if routes are accessible:</p>";

$routeTests = [
    'kepala-sekolah' => '/kepala-sekolah',
    'siswa-alpa' => '/kepala-sekolah/siswa-alpa',
    'siswa-by-status' => '/kepala-sekolah/siswa-by-status/total',
    'fallback-api-siswa-alpa' => '/kepala-sekolah/api/siswa-alpa',
    'fallback-api-siswa-by-status' => '/kepala-sekolah/api/siswa-by-status/total'
];

foreach ($routeTests as $name => $route) {
    $url = "http://$baseUrl$route";
    echo "<a href='$url' target='_blank'>🔗 Test $name: $route</a><br>";
}

echo "<hr>";

// 6. Test Authentication
echo "<h2>6. Authentication Test</h2>";
echo "<p>Note: These endpoints require authentication. You need to be logged in as kepala_sekolah.</p>";

echo "<p><strong>Manual Test Steps:</strong></p>";
echo "<ol>";
echo "<li>Login sebagai kepala sekolah</li>";
echo "<li>Buka dashboard</li>";
echo "<li>Klik panel yang tidak berfungsi</li>";
echo "<li>Buka browser console (F12) untuk melihat error</li>";
echo "<li>Buka Network tab untuk melihat request yang gagal</li>";
echo "</ol>";

echo "<hr>";

// 7. Test JavaScript Console
echo "<h2>7. JavaScript Console Test</h2>";
echo "<p>Buka browser console (F12) dan jalankan kode berikut:</p>";
echo "<pre>";
echo "// Test fetch to different URLs
const testUrls = [
    '/kepala-sekolah/siswa-alpa',
    '/kepala-sekolah/api/siswa-alpa',
    window.location.pathname.replace('/kepala-sekolah', '') + '/kepala-sekolah/siswa-alpa',
    window.location.pathname.replace('/kepala-sekolah', '') + '/kepala-sekolah/api/siswa-alpa'
];

testUrls.forEach((url, index) => {
    console.log('Testing URL ' + (index + 1) + ':', url);
    fetch(url)
        .then(response => {
            console.log('URL ' + (index + 1) + ' - Status:', response.status);
            if (response.ok) {
                return response.json();
            }
            throw new Error('HTTP ' + response.status);
        })
        .then(data => {
            console.log('URL ' + (index + 1) + ' - Success:', data);
        })
        .catch(error => {
            console.error('URL ' + (index + 1) + ' - Error:', error.message);
        });
});";
echo "</pre>";

echo "<hr>";

// 8. Test .htaccess
echo "<h2>8. .htaccess Test</h2>";
echo "<p>Checking if .htaccess files exist:</p>";

$htaccessFiles = [
    '../.htaccess' => 'Root .htaccess',
    '.htaccess' => 'Public .htaccess'
];

foreach ($htaccessFiles as $file => $description) {
    if (file_exists($file)) {
        echo "✅ $description exists<br>";
        $content = file_get_contents($file);
        if (strpos($content, 'RewriteEngine On') !== false) {
            echo "✅ $description has RewriteEngine On<br>";
        } else {
            echo "❌ $description missing RewriteEngine On<br>";
        }
    } else {
        echo "❌ $description not found<br>";
    }
}

echo "<hr>";

// 9. Test Subfolder Detection
echo "<h2>9. Subfolder Detection Test</h2>";
echo "<p><strong>Current URL:</strong> " . $_SERVER['REQUEST_URI'] . "<br>";
echo "<strong>Script Name:</strong> " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "<strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "<br>";

// Detect if we're in a subfolder
$scriptPath = $_SERVER['SCRIPT_NAME'];
if (strpos($scriptPath, '/presensi_siswa') !== false) {
    echo "✅ Detected subfolder: presensi_siswa<br>";
    echo "⚠️ This might cause routing issues<br>";
} else {
    echo "✅ No subfolder detected<br>";
}

echo "<hr>";

// 10. Recommendations
echo "<h2>10. Recommendations</h2>";
echo "<p><strong>If the panel still doesn't work:</strong></p>";
echo "<ol>";
echo "<li>Check browser console for JavaScript errors</li>";
echo "<li>Check Network tab for failed requests</li>";
echo "<li>Verify .htaccess files are properly configured</li>";
echo "<li>Check if mod_rewrite is enabled on hosting</li>";
echo "<li>Try accessing the API endpoints directly</li>";
echo "<li>Check hosting error logs</li>";
echo "</ol>";

echo "<hr>";
echo "<p><strong>Debug completed at:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>
