<?php
// Debug file khusus untuk Panel Kepala Sekolah
// File ini akan membantu mendiagnosis masalah HTTP 404

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers untuk debugging
header('Content-Type: text/html; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Debug Panel Kepala Sekolah</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .warning { background: #fff3cd; border-color: #ffeaa7; color: #856404; }
        .info { background: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        .test-button { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin: 5px; }
        .test-button:hover { background: #0056b3; }
        .result { margin: 10px 0; padding: 10px; border-radius: 3px; background: #f8f9fa; border-left: 4px solid #007bff; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
        .url-list { background: #e9ecef; padding: 10px; border-radius: 3px; margin: 10px 0; }
        .step { background: #fff3cd; padding: 10px; margin: 10px 0; border-radius: 3px; border-left: 4px solid #ffc107; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 Debug Panel Kepala Sekolah</h1>
        <p><strong>Status:</strong> Masih mengalami HTTP 404 error</p>
        
        <div class='section info'>
            <h3>📋 Informasi Server</h3>
            <p><strong>PHP Version:</strong> " . phpversion() . "</p>
            <p><strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>
            <p><strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "</p>
            <p><strong>Script Path:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'Unknown') . "</p>
            <p><strong>Request URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "</p>
            <p><strong>Current Directory:</strong> " . getcwd() . "</p>
        </div>

        <div class='section warning'>
            <h3>⚠️ Langkah Troubleshooting Wajib</h3>
            <div class='step'>
                <strong>Langkah 1:</strong> Buka Developer Tools di browser (F12) → Console tab
            </div>
            <div class='step'>
                <strong>Langkah 2:</strong> Klik panel Kepala Sekolah yang bermasalah
            </div>
            <div class='step'>
                <strong>Langkah 3:</strong> Lihat error message di console
            </div>
            <div class='step'>
                <strong>Langkah 4:</strong> Copy error message dan kirim ke saya
            </div>
        </div>

        <div class='section'>
            <h3>🧪 Test API Endpoints</h3>
            <p>Klik tombol di bawah untuk test setiap endpoint:</p>
            
            <button class='test-button' onclick='testEndpoint(\"/test-api\")'>Test /test-api</button>
            <button class='test-button' onclick='testEndpoint(\"/test-kepala-sekolah\")'>Test /test-kepala-sekolah</button>
            <button class='test-button' onclick='testEndpoint(\"/api/kepala-sekolah/siswa-alpa\")'>Test /api/kepala-sekolah/siswa-alpa</button>
            <button class='test-button' onclick='testEndpoint(\"/kepala-sekolah/api/siswa-alpa\")'>Test /kepala-sekolah/api/siswa-alpa</button>
            
            <div id='test-results'></div>
        </div>

        <div class='section'>
            <h3>🔗 Test dengan cURL (Server-side)</h3>
            <p>Test endpoint menggunakan PHP cURL:</p>";

// Test dengan cURL
$endpoints = [
    '/test-api',
    '/test-kepala-sekolah', 
    '/api/kepala-sekolah/siswa-alpa',
    '/kepala-sekolah/api/siswa-alpa'
];

foreach ($endpoints as $endpoint) {
    echo "<div class='result'>";
    echo "<strong>Testing: $endpoint</strong><br>";
    
    // Buat URL lengkap
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $url = $protocol . '://' . $host . $endpoint;
    
    echo "URL: $url<br>";
    
    // Test dengan cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "<span style='color: red;'>❌ cURL Error: $error</span>";
    } else {
        echo "<span style='color: green;'>✅ HTTP Code: $httpCode</span><br>";
        if ($httpCode == 200) {
            echo "<span style='color: green;'>✅ Response: " . substr($response, 0, 200) . "...</span>";
        } else {
            echo "<span style='color: red;'>❌ Response: " . substr($response, 0, 200) . "...</span>";
        }
    }
    echo "</div>";
}

echo "
        <div class='section'>
            <h3>📁 File Structure Check</h3>
            <p>Memeriksa struktur file penting:</p>";

// Check important files
$importantFiles = [
    '../routes/web.php',
    '../app/Http/Controllers/KepalaSekolah/DashboardController.php',
    '../resources/views/kepala-sekolah/dashboard.blade.php',
    '../.htaccess'
];

foreach ($importantFiles as $file) {
    if (file_exists($file)) {
        echo "<div class='result success'>✅ $file - EXISTS</div>";
    } else {
        echo "<div class='result error'>❌ $file - NOT FOUND</div>";
    }
}

echo "
        </div>

        <div class='section'>
            <h3>🔧 Test JavaScript Function</h3>
            <p>Test fungsi JavaScript yang digunakan di dashboard:</p>
            <button class='test-button' onclick='testJavaScriptFunction()'>Test JavaScript Function</button>
            <div id='js-test-results'></div>
        </div>

        <div class='section info'>
            <h3>📝 URL yang Dicoba JavaScript</h3>
            <p>JavaScript akan mencoba URL berikut secara berurutan:</p>
            <div class='url-list'>";

// Generate URL list yang akan dicoba JavaScript
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
$isSubfolder = strpos($currentPath, '/presensi_siswa') !== false;
$basePath = $isSubfolder ? '/presensi_siswa' : '';

$urls = [
    "Authenticated Routes:",
    "  - ${basePath}/kepala-sekolah/siswa-by-status/{status}",
    "  - ${basePath}/kepala-sekolah/api/siswa-by-status/{status}",
    "  - /kepala-sekolah/siswa-by-status/{status}",
    "  - /kepala-sekolah/api/siswa-by-status/{status}",
    "",
    "Public API Routes (No Auth):",
    "  - ${basePath}/api/kepala-sekolah/siswa-by-status/{status}",
    "  - /api/kepala-sekolah/siswa-by-status/{status}",
    "",
    "Full Origin URLs:",
    "  - " . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "/kepala-sekolah/siswa-by-status/{status}",
    "  - " . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "/api/kepala-sekolah/siswa-by-status/{status}"
];

foreach ($urls as $url) {
    echo htmlspecialchars($url) . "<br>";
}

echo "
            </div>
        </div>

        <div class='section warning'>
            <h3>🚨 Jika Masih Error</h3>
            <p>Lakukan hal berikut:</p>
            <ol>
                <li>Buka Developer Tools (F12) → Console</li>
                <li>Klik panel yang bermasalah</li>
                <li>Copy semua error message</li>
                <li>Kirim error message lengkap ke saya</li>
                <li>Jangan lupa sertakan screenshot console</li>
            </ol>
        </div>
    </div>

    <script>
        async function testEndpoint(endpoint) {
            const resultsDiv = document.getElementById('test-results');
            const resultDiv = document.createElement('div');
            resultDiv.className = 'result';
            resultDiv.innerHTML = '<strong>Testing: ' + endpoint + '</strong><br>Loading...';
            resultsDiv.appendChild(resultDiv);
            
            try {
                const response = await fetch(endpoint);
                const data = await response.text();
                
                if (response.ok) {
                    resultDiv.innerHTML = '<strong>Testing: ' + endpoint + '</strong><br>' +
                        '<span style=\"color: green;\">✅ Success! HTTP ' + response.status + '</span><br>' +
                        '<strong>Response:</strong><br><pre>' + data.substring(0, 300) + '</pre>';
                } else {
                    resultDiv.innerHTML = '<strong>Testing: ' + endpoint + '</strong><br>' +
                        '<span style=\"color: red;\">❌ Error! HTTP ' + response.status + '</span><br>' +
                        '<strong>Response:</strong><br><pre>' + data.substring(0, 300) + '</pre>';
                }
            } catch (error) {
                resultDiv.innerHTML = '<strong>Testing: ' + endpoint + '</strong><br>' +
                    '<span style=\"color: red;\">❌ Fetch Error: ' + error.message + '</span>';
            }
        }

        function testJavaScriptFunction() {
            const resultsDiv = document.getElementById('js-test-results');
            resultsDiv.innerHTML = '';
            
            // Test URL construction
            const currentPath = window.location.pathname;
            const isSubfolder = currentPath.includes('/presensi_siswa');
            const basePath = isSubfolder ? '/presensi_siswa' : '';
            
            const testUrls = [
                basePath + '/kepala-sekolah/siswa-by-status/hadir',
                basePath + '/api/kepala-sekolah/siswa-by-status/hadir',
                window.location.origin + '/kepala-sekolah/siswa-by-status/hadir',
                window.location.origin + '/api/kepala-sekolah/siswa-by-status/hadir'
            ];
            
            const resultDiv = document.createElement('div');
            resultDiv.className = 'result';
            resultDiv.innerHTML = '<strong>JavaScript URL Construction Test:</strong><br>';
            
            testUrls.forEach((url, index) => {
                resultDiv.innerHTML += '<br><strong>URL ' + (index + 1) + ':</strong> ' + url;
            });
            
            resultsDiv.appendChild(resultDiv);
        }

        // Auto-test saat halaman dimuat
        window.addEventListener('load', function() {
            console.log('Debug Panel Kepala Sekolah loaded');
            console.log('Current pathname:', window.location.pathname);
            console.log('Current origin:', window.location.origin);
            console.log('Current host:', window.location.host);
        });
    </script>
</body>
</html>";
?>
