<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Panel Filtering - Kepala Sekolah</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .debug-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 8px; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .warning { background-color: #fff3cd; border-color: #ffeaa7; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
        .info { background-color: #d1ecf1; border-color: #bee5eb; }
        button { padding: 10px 20px; margin: 5px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-success { background-color: #28a745; color: white; }
        .btn-warning { background-color: #ffc107; color: black; }
        .btn-danger { background-color: #dc3545; color: white; }
        pre { background-color: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
        .status-badge { display: inline-block; padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; }
        .status-tepat_waktu { background-color: #28a745; color: white; }
        .status-terlambat { background-color: #ffc107; color: black; }
        .status-sakit { background-color: #17a2b8; color: white; }
        .status-izin { background-color: #e83e8c; color: white; }
        .status-alpa { background-color: #6c757d; color: white; }
        .status-total { background-color: #007bff; color: white; }
        .status-absen { background-color: #dc3545; color: white; }
    </style>
</head>
<body>
    <h1>🔍 Debug Panel Filtering - Kepala Sekolah</h1>
    <p><strong>Masalah:</strong> Panel menampilkan semua siswa alih-alih siswa yang difilter berdasarkan status</p>
    
    <div class="debug-section info">
        <h3>📋 Langkah Debug yang Harus Dilakukan</h3>
        <ol>
            <li><strong>Buka Developer Tools (F12)</strong> di dashboard Kepala Sekolah</li>
            <li><strong>Pilih tab Console</strong> untuk melihat log</li>
            <li><strong>Klik salah satu panel</strong> (misalnya "Hadir Hari Ini")</li>
            <li><strong>Periksa log di console</strong> untuk melihat data yang diterima</li>
            <li><strong>Copy paste log tersebut</strong> ke sini</li>
        </ol>
    </div>

    <div class="debug-section">
        <h3>🧪 Test API Endpoints</h3>
        <p>Klik tombol di bawah untuk test API endpoints:</p>
        
        <div style="margin: 10px 0;">
            <button class="btn-primary" onclick="testAPI('tepat_waktu')">Test: Tepat Waktu</button>
            <button class="btn-warning" onclick="testAPI('terlambat')">Test: Terlambat</button>
            <button class="btn-danger" onclick="testAPI('alpa')">Test: Alpa</button>
            <button class="btn-success" onclick="testAPI('total')">Test: Total</button>
        </div>
        
        <div id="apiResults"></div>
    </div>

    <div class="debug-section">
        <h3>🔗 URL yang Akan Dicoba JavaScript</h3>
        <p>JavaScript akan mencoba URL berikut secara berurutan:</p>
        <div id="urlList"></div>
    </div>

    <div class="debug-section">
        <h3>📊 Data yang Diterima</h3>
        <p>Hasil test API akan ditampilkan di sini:</p>
        <pre id="dataDisplay">Belum ada data</pre>
    </div>

    <div class="debug-section warning">
        <h3>⚠️ Kemungkinan Penyebab Masalah</h3>
        <ul>
            <li><strong>API mengembalikan data yang salah</strong> - Server mengirim semua siswa alih-alih yang difilter</li>
            <li><strong>JavaScript salah memproses data</strong> - Data benar tapi ditampilkan salah</li>
            <li><strong>Route yang salah diakses</strong> - JavaScript mengakses endpoint yang tidak sesuai</li>
            <li><strong>Masalah autentikasi</strong> - Session tidak valid sehingga data yang dikembalikan salah</li>
        </ul>
    </div>

    <div class="debug-section success">
        <h3>✅ Solusi yang Sudah Diterapkan</h3>
        <ul>
            <li><strong>Urutan URL diubah</strong> - Public API routes dicoba terlebih dahulu</li>
            <li><strong>Logging ditingkatkan</strong> - Console akan menampilkan detail data yang diterima</li>
            <li><strong>Fallback URLs</strong> - Multiple URL options untuk reliability</li>
        </ul>
    </div>

    <script>
        // Detect subfolder hosting
        const currentUrl = window.location.href;
        const currentPath = window.location.pathname;
        let basePath = '';
        
        if (currentUrl.includes('/presensi_siswa/') || currentPath.includes('/presensi_siswa')) {
            basePath = '/presensi_siswa';
            console.log('✅ Detected subfolder hosting, using basePath:', basePath);
        } else {
            console.log('✅ No subfolder detected, using root path');
        }

        // Display URLs that JavaScript will try
        function displayURLs() {
            const statuses = ['tepat_waktu', 'terlambat', 'alpa', 'total'];
            let html = '';
            
            statuses.forEach(status => {
                html += `<h4>Status: <span class="status-badge status-${status}">${status}</span></h4>`;
                html += '<ol>';
                
                const urls = [
                    `${basePath}/api/kepala-sekolah/siswa-by-status/${status}`,
                    `/api/kepala-sekolah/siswa-by-status/${status}`,
                    `${basePath}/kepala-sekolah/siswa-by-status/${status}`,
                    `${basePath}/kepala-sekolah/api/siswa-by-status/${status}`,
                    `/kepala-sekolah/siswa-by-status/${status}`,
                    `/kepala-sekolah/api/siswa-by-status/${status}`
                ];
                
                urls.forEach((url, index) => {
                    html += `<li><code>${url}</code></li>`;
                });
                
                html += '</ol>';
            });
            
            document.getElementById('urlList').innerHTML = html;
        }

        // Test API endpoints
        async function testAPI(status) {
            const resultsDiv = document.getElementById('apiResults');
            const dataDisplay = document.getElementById('dataDisplay');
            
            resultsDiv.innerHTML = `<p>🔄 Testing API for status: <span class="status-badge status-${status}">${status}</span></p>`;
            dataDisplay.textContent = 'Loading...';
            
            const urls = [
                `${basePath}/api/kepala-sekolah/siswa-by-status/${status}`,
                `/api/kepala-sekolah/siswa-by-status/${status}`,
                `${basePath}/kepala-sekolah/siswa-by-status/${status}`,
                `${basePath}/kepala-sekolah/api/siswa-by-status/${status}`,
                `/kepala-sekolah/siswa-by-status/${status}`,
                `/kepala-sekolah/api/siswa-by-status/${status}`
            ];
            
            let success = false;
            let lastError = '';
            
            for (let i = 0; i < urls.length; i++) {
                const url = urls[i];
                try {
                    console.log(`🧪 Testing URL ${i + 1}: ${url}`);
                    
                    const response = await fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });
                    
                    console.log(`✅ URL ${i + 1} response:`, response.status, response.statusText);
                    
                    if (response.ok) {
                        const data = await response.json();
                        console.log(`🎯 Data received from ${url}:`, data);
                        
                        // Display results
                        resultsDiv.innerHTML = `
                            <div class="success">
                                <h4>✅ Success!</h4>
                                <p><strong>Working URL:</strong> <code>${url}</code></p>
                                <p><strong>Status Code:</strong> ${response.status}</p>
                                <p><strong>Data Count:</strong> ${Array.isArray(data) ? data.length : 'Not an array'}</p>
                                <p><strong>Data Type:</strong> ${typeof data}</p>
                            </div>
                        `;
                        
                        // Display data
                        dataDisplay.textContent = JSON.stringify(data, null, 2);
                        
                        success = true;
                        break;
                    } else {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                } catch (error) {
                    console.error(`❌ URL ${i + 1} failed:`, error.message);
                    lastError = error.message;
                    
                    if (i === urls.length - 1) {
                        // All URLs failed
                        resultsDiv.innerHTML = `
                            <div class="error">
                                <h4>❌ All URLs Failed</h4>
                                <p><strong>Last Error:</strong> ${lastError}</p>
                                <p><strong>Tested URLs:</strong></p>
                                <ul>
                                    ${urls.map(url => `<li><code>${url}</code></li>`).join('')}
                                </ul>
                            </div>
                        `;
                        dataDisplay.textContent = `All URLs failed. Last error: ${lastError}`;
                    }
                }
            }
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            displayURLs();
            console.log('🚀 Debug Panel Filtering loaded');
            console.log('📍 Current URL:', window.location.href);
            console.log('📍 Base Path:', basePath);
        });
    </script>
</body>
</html>
