<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST URLS DI HOSTING ===\n\n";

// Test URL generation
echo "1. Test URL Generation:\n";
echo "URL untuk /api/siswa-by-status/tepat_waktu: " . url('/api/siswa-by-status/tepat_waktu') . "\n";
echo "URL untuk /guru: " . url('/guru') . "\n";
echo "URL untuk /guru/presensi: " . url('/guru/presensi') . "\n";
echo "APP_URL: " . config('app.url') . "\n\n";

// Test route availability
echo "2. Test Route Availability:\n";
$router = app('router');
$routes = $router->getRoutes();

$testRoutes = [
    '/api/siswa-by-status/{status}',
    '/guru',
    '/guru/presensi',
    '/guru/presensi/siswa-by-status/{status}'
];

foreach ($testRoutes as $testRoute) {
    $found = false;
    foreach ($routes as $route) {
        if (strpos($route->uri(), str_replace('{status}', 'test', $testRoute)) !== false) {
            echo "✓ Route {$testRoute} ditemukan\n";
            $found = true;
            break;
        }
    }
    if (!$found) {
        echo "✗ Route {$testRoute} TIDAK ditemukan\n";
    }
}

echo "\n3. Test Database Connection:\n";
try {
    \DB::connection()->getPdo();
    echo "✓ Database connection berhasil\n";
    
    // Test query
    $siswaCount = \App\Models\Siswa::count();
    echo "✓ Total siswa: {$siswaCount}\n";
    
    $presensiCount = \App\Models\Presensi::whereDate('tanggal', today())->count();
    echo "✓ Presensi hari ini: {$presensiCount}\n";
    
} catch (\Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
}

echo "\n4. Test API Endpoint:\n";
try {
    $request = \Illuminate\Http\Request::create('/api/siswa-by-status/tepat_waktu', 'GET');
    $response = app()->handle($request);
    
    echo "Status Code: " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() === 200) {
        echo "✓ API endpoint berfungsi\n";
        $content = $response->getContent();
        $data = json_decode($content, true);
        echo "Data count: " . count($data) . "\n";
    } else {
        echo "✗ API endpoint error\n";
    }
    
} catch (\Exception $e) {
    echo "✗ API test error: " . $e->getMessage() . "\n";
}

echo "\n=== SELESAI ===\n";
echo "\nJika semua test berhasil, berarti masalah ada di:\n";
echo "1. .htaccess tidak aktif di hosting\n";
echo "2. mod_rewrite tidak aktif di hosting\n";
echo "3. URL di JavaScript masih salah\n"; 