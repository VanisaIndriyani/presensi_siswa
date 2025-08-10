<?php
// Debug API sederhana untuk testing
// Akses melalui: http://yourdomain.com/debug_api_simple.php

echo "<h1>🔍 Debug API Sederhana</h1>";
echo "<hr>";

// 1. Test basic API
echo "<h2>1. Test Basic API</h2>";
echo "<a href='/test-api' target='_blank'>Test Basic API</a><br>";
echo "<a href='/test-kepala-sekolah' target='_blank'>Test Kepala Sekolah API</a><br>";

echo "<hr>";

// 2. Test Kepala Sekolah endpoints
echo "<h2>2. Test Kepala Sekolah Endpoints</h2>";
echo "<h3>Authenticated Routes:</h3>";
echo "<a href='/kepala-sekolah/api/siswa-alpa' target='_blank'>/kepala-sekolah/api/siswa-alpa</a><br>";
echo "<a href='/kepala-sekolah/api/siswa-by-status/total' target='_blank'>/kepala-sekolah/api/siswa-by-status/total</a><br>";

echo "<h3>Public API Routes (No Auth):</h3>";
echo "<a href='/api/kepala-sekolah/siswa-alpa' target='_blank'>/api/kepala-sekolah/siswa-alpa</a><br>";
echo "<a href='/api/kepala-sekolah/siswa-by-status/total' target='_blank'>/api/kepala-sekolah/siswa-by-status/total</a><br>";

echo "<hr>";

// 3. Test dengan JavaScript
echo "<h2>3. Test dengan JavaScript</h2>";
echo "<button onclick='testAPI()'>Test API dengan JavaScript</button>";
echo "<div id='result'></div>";

echo "<hr>";

// 4. Test dengan cURL
echo "<h2>4. Test dengan cURL</h2>";
if (function_exists('curl_init')) {
    echo "✅ cURL tersedia<br>";
    
    // Test basic API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://' . $_SERVER['HTTP_HOST'] . '/test-api');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Test API Response (HTTP $httpCode):<br>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
} else {
    echo "❌ cURL tidak tersedia<br>";
}

echo "<hr>";

// 5. Environment info
echo "<h2>5. Environment Info</h2>";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Path: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";

echo "<hr>";
echo "<p><strong>Catatan:</strong> File ini untuk testing. Hapus setelah selesai.</p>";

?>

<script>
function testAPI() {
    const resultDiv = document.getElementById('result');
    resultDiv.innerHTML = 'Testing...';
    
    // Test multiple URLs
    const urls = [
        '/test-api',
        '/api/kepala-sekolah/siswa-alpa',
        '/kepala-sekolah/api/siswa-alpa'
    ];
    
    let results = [];
    
    urls.forEach((url, index) => {
        fetch(url)
            .then(response => {
                const status = response.status;
                const statusText = response.statusText;
                results.push(`${url}: HTTP ${status} (${statusText})`);
                
                if (results.length === urls.length) {
                    resultDiv.innerHTML = '<h4>Results:</h4>' + results.join('<br>');
                }
            })
            .catch(error => {
                results.push(`${url}: ERROR - ${error.message}`);
                
                if (results.length === urls.length) {
                    resultDiv.innerHTML = '<h4>Results:</h4>' + results.join('<br>');
                }
            });
    });
}
</script>
