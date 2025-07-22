<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Membuat User Kepala Sekolah ===\n";

// Cek apakah user sudah ada
$existingUser = User::where('email', 'kepalasekolah@gmail.com')->first();

if ($existingUser) {
    echo "User Kepala Sekolah sudah ada!\n";
    echo "Email: kepalasekolah@gmail.com\n";
    echo "Role: " . $existingUser->role . "\n";
    
    // Update password jika perlu
    $existingUser->password = Hash::make('kepalasekolah123');
    $existingUser->save();
    echo "Password telah diupdate: kepalasekolah123\n";
} else {
    // Buat user baru
    User::create([
        'name' => 'Kepala Sekolah',
        'email' => 'kepalasekolah@gmail.com',
        'password' => Hash::make('kepalasekolah123'),
        'role' => 'kepala_sekolah',
    ]);
    
    echo "User Kepala Sekolah berhasil dibuat!\n";
    echo "Email: kepalasekolah@gmail.com\n";
    echo "Password: kepalasekolah123\n";
    echo "Role: kepala_sekolah\n";
}

echo "\n=== Selesai ===\n";
echo "Sekarang Anda bisa login dengan:\n";
echo "Email: kepalasekolah@gmail.com\n";
echo "Password: kepalasekolah123\n"; 