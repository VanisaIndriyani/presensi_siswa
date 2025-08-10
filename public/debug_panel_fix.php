<?php
// Debug file untuk testing panel Kepala Sekolah
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Panel Kepala Sekolah</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .debug-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 8px; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
        .info { background-color: #d1ecf1; border-color: #bee5eb; }
        .url-test { margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 4px; }
        button { padding: 8px 16px; margin: 5px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔧 Debug Panel Kepala Sekolah</h1>
    
    <div class="debug-section info">
        <h3>📍 Informasi Server</h3>
        <p><strong>Current URL:</strong> <?php echo $_SERVER['REQUEST_URI']; ?></p>
        <p><strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT']; ?></p>
        <p><strong>Script Name:</strong> <?php echo $_SERVER['SCRIPT_NAME']; ?></p>
        <p><strong>HTTP Host:</strong> <?php echo $_SERVER['HTTP_HOST']; ?></p>
    </div>

    <div class="debug-section info">
        <h3>🔗 URL yang Seharusnya Digunakan</h3>
        <?php
        $currentUrl = $_SERVER['REQUEST_URI'];
        $isSubfolder = strpos($currentUrl, '/presensi_siswa') !== false;
        $basePath = $isSubfolder ? '/presensi_siswa' : '';
        ?>
        
        <p><strong>Subfolder terdeteksi:</strong> <?php echo $isSubfolder ? 'Ya' : 'Tidak'; ?></p>
        <p><strong>Base Path:</strong> <?php echo $basePath ?: '/ (root)'; ?></p>
        
        <div class="url-test">
            <h4>URL untuk Panel:</h4>
            <p><code><?php echo $basePath; ?>/kepala-sekolah/siswa-by-status/tepat_waktu</code></p>
            <p><code><?php echo $basePath; ?>/kepala-sekolah/api/siswa-by-status/tepat_waktu</code></p>
            <p><code><?php echo $basePath; ?>/api/kepala-sekolah/siswa-by-status/tepat_waktu</code></p>
        </div>
    </div>

    <div class="debug-section">
        <h3>🧪 Test API Endpoints</h3>
        <button class="btn-primary" onclick="testAPI('siswa-alpa')">Test Siswa Alpa</button>
        <button class="btn-primary" onclick="testAPI('siswa-by-status/tepat_waktu')">Test Tepat Waktu</button>
        <button class="btn-success" onclick="testAllEndpoints()">Test Semua Endpoint</button>
        <div id="api-results"></div>
    </div>

    <div class="debug-section">
        <h3>📱 Test Panel Click</h3>
        <button class="btn-danger" onclick="testPanelClick()">Test Panel Click</button>
        <div id="panel-results"></div>
    </div>

    <div class="debug-section">
        <h3>📋 Console Log</h3>
        <div id="console-log" style="background: #000; color: #0f0; padding: 10px; border-radius: 4px; height: 200px; overflow-y: auto; font-family: monospace;"></div>
    </div>

    <script>
        // Override console.log untuk menampilkan di halaman
        const originalLog = console.log;
        const consoleDiv = document.getElementById('console-log');
        
        console.log = function(...args) {
            originalLog.apply(console, args);
            const message = args.map(arg => 
                typeof arg === 'object' ? JSON.stringify(arg, null, 2) : String(arg)
            ).join(' ');
            consoleDiv.innerHTML += `<div>[${new Date().toLocaleTimeString()}] ${message}</div>`;
            consoleDiv.scrollTop = consoleDiv.scrollHeight;
        };

        // Test API endpoints
        function testAPI(endpoint) {
            const currentUrl = window.location.href;
            const isSubfolder = currentUrl.includes('/presensi_siswa');
            const basePath = isSubfolder ? '/presensi_siswa' : '';
            
            const urls = [
                `${basePath}/${endpoint}`,
                `${basePath}/kepala-sekolah/${endpoint}`,
                `${basePath}/api/kepala-sekolah/${endpoint}`,
                `/${endpoint}`,
                `/kepala-sekolah/${endpoint}`,
                `/api/kepala-sekolah/${endpoint}`
            ];
            
            console.log('🧪 Testing endpoint:', endpoint);
            console.log('🔗 URLs to test:', urls);
            
            const resultsDiv = document.getElementById('api-results');
            resultsDiv.innerHTML = '<h4>Testing Results:</h4>';
            
            urls.forEach((url, index) => {
                console.log(`Testing URL ${index + 1}:`, url);
                
                fetch(url, { method: 'HEAD' })
                    .then(response => {
                        const status = response.status;
                        const color = status === 200 ? 'green' : 'red';
                        resultsDiv.innerHTML += `<p style="color: ${color};">✅ ${url}: ${status}</p>`;
                        console.log(`✅ ${url}: ${status}`);
                    })
                    .catch(error => {
                        resultsDiv.innerHTML += `<p style="color: red;">❌ ${url}: ${error.message}</p>`;
                        console.log(`❌ ${url}: ${error.message}`);
                    });
            });
        }

        // Test all endpoints
        function testAllEndpoints() {
            const endpoints = [
                'siswa-alpa',
                'siswa-by-status/tepat_waktu',
                'siswa-by-status/terlambat',
                'siswa-by-status/sakit',
                'siswa-by-status/izin',
                'siswa-by-status/alpa'
            ];
            
            endpoints.forEach(endpoint => {
                setTimeout(() => testAPI(endpoint), 1000);
            });
        }

        // Test panel click simulation
        function testPanelClick() {
            console.log('🖱️ Testing panel click simulation...');
            
            const currentUrl = window.location.href;
            const isSubfolder = currentUrl.includes('/presensi_siswa');
            const basePath = isSubfolder ? '/presensi_siswa' : '';
            
            const testUrls = [
                `${basePath}/kepala-sekolah/siswa-by-status/tepat_waktu`,
                `${basePath}/api/kepala-sekolah/siswa-by-status/tepat_waktu`
            ];
            
            const resultsDiv = document.getElementById('panel-results');
            resultsDiv.innerHTML = '<h4>Panel Click Test Results:</h4>';
            
            testUrls.forEach((url, index) => {
                console.log(`Testing panel URL ${index + 1}:`, url);
                
                fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    resultsDiv.innerHTML += `<p style="color: green;">✅ ${url}: Success - ${JSON.stringify(data).substring(0, 100)}...</p>`;
                    console.log(`✅ Panel URL ${url} success:`, data);
                })
                .catch(error => {
                    resultsDiv.innerHTML += `<p style="color: red;">❌ ${url}: ${error.message}</p>`;
                    console.log(`❌ Panel URL ${url} failed:`, error.message);
                });
            });
        }

        // Auto-test saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Debug Panel loaded');
            console.log('📍 Current URL:', window.location.href);
            console.log('📍 Current pathname:', window.location.pathname);
            console.log('📍 Origin:', window.location.origin);
            
            // Check subfolder
            const currentUrl = window.location.href;
            if (currentUrl.includes('/presensi_siswa')) {
                console.log('⚠️ Detected subfolder hosting: presensi_siswa');
                console.log('✅ Base path: /presensi_siswa');
            } else {
                console.log('✅ No subfolder detected');
                console.log('✅ Base path: / (root)');
            }
        });
    </script>
</body>
</html>
