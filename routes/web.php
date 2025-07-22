<?php

use Illuminate\Support\Facades\Route;

// Redirect root to login if not authenticated
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->role === 'admin') {
            return redirect('/admin');
        } elseif ($user->role === 'guru') {
            return redirect('/guru');
        } elseif ($user->role === 'kepala_sekolah') {
            return redirect('/kepala-sekolah');
        } else {
            return redirect('/siswa');
        }
    }
    return redirect('/login');
});

// Auth routes
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('siswa', App\Http\Controllers\Admin\SiswaController::class);
    Route::get('siswa/{siswa}/idcard', [App\Http\Controllers\Admin\SiswaController::class, 'idCard'])->name('siswa.idcard');
    Route::resource('guru', App\Http\Controllers\Admin\GuruController::class);
    Route::get('guru/{guru}/check-account', [App\Http\Controllers\Admin\GuruController::class, 'checkAccount'])->name('guru.checkAccount');
    Route::resource('presensi', App\Http\Controllers\Admin\PresensiController::class);
    Route::resource('libur', App\Http\Controllers\Admin\LiburController::class);
    Route::resource('laporan', App\Http\Controllers\Admin\LaporanController::class)->only(['index']);
    Route::post('libur/jam-masuk', [App\Http\Controllers\Admin\LiburController::class, 'updateJamMasuk'])->name('libur.updateJamMasuk');
    Route::get('laporan/export-excel', [App\Http\Controllers\Admin\LaporanController::class, 'exportExcel'])->name('laporan.exportExcel');
    Route::get('laporan/export-pdf', [App\Http\Controllers\Admin\LaporanController::class, 'exportPdf'])->name('laporan.exportPdf');
    Route::get('presensi/api', [App\Http\Controllers\Admin\PresensiController::class, 'api'])->name('presensi.api');
    Route::get('siswa/api', [App\Http\Controllers\Admin\SiswaController::class, 'api'])->name('siswa.api');
});

// Guru routes
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/', [App\Http\Controllers\Guru\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pemindai-qr', [App\Http\Controllers\Guru\DashboardController::class, 'pemindaiQr'])->name('pemindaiqr');
    Route::get('/riwayat', [App\Http\Controllers\Guru\DashboardController::class, 'riwayat'])->name('riwayat');
    Route::resource('presensi', App\Http\Controllers\Guru\PresensiController::class);
    Route::post('/presensi/scan-qr', [App\Http\Controllers\Guru\PresensiController::class, 'scanQr'])->name('presensi.scanQr');
    Route::get('/presensi/api', [App\Http\Controllers\Guru\PresensiController::class, 'api'])->name('presensi.api');
    Route::get('/presensi/siswa-by-status/{status}', [App\Http\Controllers\Guru\PresensiController::class, 'getSiswaByStatus'])->name('presensi.siswaByStatus');
});

// Siswa routes (opsional login)
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/', [App\Http\Controllers\Siswa\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('presensi', App\Http\Controllers\Siswa\PresensiController::class);
});

// Kepala Sekolah routes
Route::middleware(['auth', 'role:kepala_sekolah'])->prefix('kepala-sekolah')->name('kepala-sekolah.')->group(function () {
    Route::get('/', [App\Http\Controllers\KepalaSekolah\DashboardController::class, 'index'])->name('dashboard');
    Route::get('siswa-alpa', [App\Http\Controllers\KepalaSekolah\DashboardController::class, 'getSiswaAlpa'])->name('siswa.alpa');
    Route::resource('laporan', App\Http\Controllers\KepalaSekolah\LaporanController::class)->only(['index']);
    Route::get('laporan/export-excel', [App\Http\Controllers\KepalaSekolah\LaporanController::class, 'exportExcel'])->name('laporan.exportExcel');
    Route::get('laporan/export-pdf', [App\Http\Controllers\KepalaSekolah\LaporanController::class, 'exportPdf'])->name('laporan.exportPdf');
});

// Route QR code siswa (gambar PNG untuk modal show)
Route::get('/admin/siswa/{siswa}/qrcode', function(App\Models\Siswa $siswa) {
    return \QrCode::format('png')->size(200)->generate($siswa->qr_code);
});
