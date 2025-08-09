<?php
// Debug file untuk kepala sekolah dashboard
// Akses via: yourdomain.com/debug_kepala_sekolah.php

require_once 'vendor/autoload.php';

use App\Models\Presensi;
use App\Models\Siswa;
use Carbon\Carbon;

echo "<h1>Debug Kepala Sekolah Dashboard</h1>";
echo "<p>Waktu: " . now() . "</p>";

try {
    // Test database connection
    echo "<h2>1. Test Database Connection</h2>";
    $test = \DB::connection()->getPdo();
    echo "✅ Database connection: OK<br>";
    
    // Test models
    echo "<h2>2. Test Models</h2>";
    
    $totalSiswa = Siswa::count();
    echo "✅ Total Siswa: " . $totalSiswa . "<br>";
    
    $today = Carbon::today();
    echo "✅ Today: " . $today->format('Y-m-d') . "<br>";
    
    // Test presensi data
    echo "<h2>3. Test Presensi Data</h2>";
    
    $hadirHariIni = Presensi::whereDate('tanggal', $today)
        ->where('status', 'tepat_waktu')
        ->count();
    echo "✅ Hadir Hari Ini: " . $hadirHariIni . "<br>";
    
    $terlambatHariIni = Presensi::whereDate('tanggal', $today)
        ->where('status', 'terlambat')
        ->count();
    echo "✅ Terlambat Hari Ini: " . $terlambatHariIni . "<br>";
    
    $sakitHariIni = Presensi::whereDate('tanggal', $today)
        ->where('status', 'sakit')
        ->count();
    echo "✅ Sakit Hari Ini: " . $sakitHariIni . "<br>";
    
    $izinHariIni = Presensi::whereDate('tanggal', $today)
        ->where('status', 'izin')
        ->count();
    echo "✅ Izin Hari Ini: " . $izinHariIni . "<br>";
    
    $alpaHariIni = Presensi::whereDate('tanggal', $today)
        ->where('status', 'alpa')
        ->count();
    echo "✅ Alpa Hari Ini: " . $alpaHariIni . "<br>";
    
    // Test API endpoints
    echo "<h2>4. Test API Endpoints</h2>";
    
    // Test siswa alpa
    $siswaAlpa = Presensi::whereDate('tanggal', $today)
        ->where('status', 'alpa')
        ->with('siswa')
        ->get()
        ->map(function($presensi) {
            return [
                'nama' => $presensi->siswa->nama ?? '-',
                'nisn' => $presensi->siswa->nisn ?? '-',
                'kelas' => $presensi->siswa->kelas ?? '-',
                'jenis_kelamin' => $presensi->siswa->jenis_kelamin ?? '-'
            ];
        });
    
    echo "✅ Siswa Alpa Count: " . $siswaAlpa->count() . "<br>";
    echo "✅ Siswa Alpa Data: <pre>" . json_encode($siswaAlpa->toArray(), JSON_PRETTY_PRINT) . "</pre><br>";
    
    // Test siswa by status
    $statuses = ['total', 'tepat_waktu', 'terlambat', 'sakit', 'izin', 'alpa', 'absen'];
    
    foreach ($statuses as $status) {
        echo "<h3>Status: " . $status . "</h3>";
        
        if ($status === 'total') {
            $siswas = Siswa::orderBy('nama')->get()->map(function($siswa) {
                return [
                    'nama' => $siswa->nama,
                    'nisn' => $siswa->nisn,
                    'kelas' => $siswa->kelas,
                    'jenis_kelamin' => $siswa->jenis_kelamin
                ];
            });
        } elseif ($status === 'absen') {
            $siswaHadir = Presensi::whereDate('tanggal', $today)->pluck('siswa_id');
            $siswas = Siswa::whereNotIn('id', $siswaHadir)->orderBy('nama')->get()->map(function($siswa) {
                return [
                    'nama' => $siswa->nama,
                    'nisn' => $siswa->nisn,
                    'kelas' => $siswa->kelas,
                    'jenis_kelamin' => $siswa->jenis_kelamin
                ];
            });
        } else {
            $siswas = Presensi::whereDate('tanggal', $today)
                ->where('status', $status)
                ->with('siswa')
                ->get()
                ->map(function($presensi) {
                    return [
                        'nama' => $presensi->siswa->nama ?? '-',
                        'nisn' => $presensi->siswa->nisn ?? '-',
                        'kelas' => $presensi->siswa->kelas ?? '-',
                        'jenis_kelamin' => $presensi->siswa->jenis_kelamin ?? '-'
                    ];
                });
        }
        
        echo "✅ Count: " . $siswas->count() . "<br>";
        echo "✅ Data: <pre>" . json_encode($siswas->toArray(), JSON_PRETTY_PRINT) . "</pre><br>";
    }
    
    // Test routes
    echo "<h2>5. Test Routes</h2>";
    echo "✅ Route /kepala-sekolah/siswa-alpa should work<br>";
    echo "✅ Route /kepala-sekolah/siswa-by-status/{status} should work<br>";
    
    // Test environment
    echo "<h2>6. Environment Info</h2>";
    echo "✅ App Environment: " . env('APP_ENV', 'not set') . "<br>";
    echo "✅ App Debug: " . (env('APP_DEBUG', false) ? 'true' : 'false') . "<br>";
    echo "✅ Database: " . env('DB_CONNECTION', 'not set') . "<br>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error Found</h2>";
    echo "Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
    echo "Trace: <pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>7. Manual Test Links</h2>";
echo "<a href='/kepala-sekolah/siswa-alpa' target='_blank'>Test /kepala-sekolah/siswa-alpa</a><br>";
echo "<a href='/kepala-sekolah/siswa-by-status/total' target='_blank'>Test /kepala-sekolah/siswa-by-status/total</a><br>";
echo "<a href='/kepala-sekolah/siswa-by-status/tepat_waktu' target='_blank'>Test /kepala-sekolah/siswa-by-status/tepat_waktu</a><br>";
echo "<a href='/kepala-sekolah/siswa-by-status/terlambat' target='_blank'>Test /kepala-sekolah/siswa-by-status/terlambat</a><br>";
echo "<a href='/kepala-sekolah/siswa-by-status/sakit' target='_blank'>Test /kepala-sekolah/siswa-by-status/sakit</a><br>";
echo "<a href='/kepala-sekolah/siswa-by-status/izin' target='_blank'>Test /kepala-sekolah/siswa-by-status/izin</a><br>";
echo "<a href='/kepala-sekolah/siswa-by-status/alpa' target='_blank'>Test /kepala-sekolah/siswa-by-status/alpa</a><br>";
echo "<a href='/kepala-sekolah/siswa-by-status/absen' target='_blank'>Test /kepala-sekolah/siswa-by-status/absen</a><br>";
?>
