<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST ROUTE SISWA BY STATUS ===\n\n";

// Test route yang ada
$router = app('router');
$routes = $router->getRoutes();

echo "Routes yang mengandung 'siswa-by-status':\n";
foreach ($routes as $route) {
    if (strpos($route->uri(), 'siswa-by-status') !== false) {
        echo "- " . $route->methods()[0] . " " . $route->uri() . " -> " . $route->getActionName() . "\n";
    }
}

echo "\n=== TEST ACCESS ROUTE ===\n";

// Test akses route
try {
    $request = \Illuminate\Http\Request::create('/guru/presensi/siswa-by-status/tepat_waktu', 'GET');
    $response = app()->handle($request);
    
    echo "Status Code: " . $response->getStatusCode() . "\n";
    echo "Response: " . $response->getContent() . "\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST CONTROLLER METHOD ===\n";

// Test controller method langsung
try {
    $controller = new \App\Http\Controllers\Guru\PresensiController();
    $result = $controller->getSiswaByStatus('tepat_waktu');
    
    echo "Controller method berhasil\n";
    echo "Result: " . $result->getContent() . "\n";
    
} catch (\Exception $e) {
    echo "Controller Error: " . $e->getMessage() . "\n";
}

echo "\n=== SELESAI ===\n"; 