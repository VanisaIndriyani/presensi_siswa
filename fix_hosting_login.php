<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FIX LOGIN HOSTING ===\n\n";

// 1. Cek koneksi database
try {
    \DB::connection()->getPdo();
    echo "✓ Koneksi database berhasil\n";
} catch (\Exception $e) {
    echo "✗ Error koneksi database: " . $e->getMessage() . "\n";
    exit;
}

// 2. Cek tabel users
try {
    $userCount = User::count();
    echo "✓ Tabel users ada, total user: $userCount\n";
} catch (\Exception $e) {
    echo "✗ Error tabel users: " . $e->getMessage() . "\n";
    echo "Jalankan: php artisan migrate\n";
    exit;
}

// 3. Cek user kepala sekolah
$kepalaSekolah = User::where('email', 'kepalasekolah@gmail.com')->first();

if ($kepalaSekolah) {
    echo "✓ User kepala sekolah ditemukan\n";
    echo "  - Name: " . $kepalaSekolah->name . "\n";
    echo "  - Email: " . $kepalaSekolah->email . "\n";
    echo "  - Role: " . $kepalaSekolah->role . "\n";
    
    // Update password
    $kepalaSekolah->password = Hash::make('kepalasekolah123');
    $kepalaSekolah->save();
    echo "✓ Password diupdate: kepalasekolah123\n";
} else {
    echo "✗ User kepala sekolah tidak ditemukan\n";
    echo "Membuat user baru...\n";
    
    try {
        User::create([
            'name' => 'Kepala Sekolah',
            'email' => 'kepalasekolah@gmail.com',
            'password' => Hash::make('kepalasekolah123'),
            'role' => 'kepala_sekolah',
        ]);
        echo "✓ User kepala sekolah berhasil dibuat\n";
    } catch (\Exception $e) {
        echo "✗ Error membuat user: " . $e->getMessage() . "\n";
    }
}

// 4. Cek semua user
echo "\n=== DAFTAR SEMUA USER ===\n";
$users = User::all(['name', 'email', 'role']);
foreach ($users as $user) {
    echo "- {$user->name} ({$user->email}) - Role: {$user->role}\n";
}

// 5. Test login
echo "\n=== TEST LOGIN ===\n";
$testUser = User::where('email', 'kepalasekolah@gmail.com')->first();
if ($testUser) {
    $testPassword = 'kepalasekolah123';
    if (Hash::check($testPassword, $testUser->password)) {
        echo "✓ Password test berhasil\n";
    } else {
        echo "✗ Password test gagal\n";
    }
}

echo "\n=== SELESAI ===\n";
echo "Coba login dengan:\n";
echo "Email: kepalasekolah@gmail.com\n";
echo "Password: kepalasekolah123\n"; 