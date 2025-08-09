# Pengaturan Jam Pulang - Sistem Presensi

## Masalah yang Diperbaiki

Sebelumnya, ketika guru tidak sengaja melakukan absen dua kali berturut-turut, sistem langsung mencatat sebagai pulang tanpa validasi yang tepat. Ini menyebabkan masalah karena:

1. **Tidak ada validasi waktu** - Siswa bisa langsung pulang setelah masuk
2. **Tidak ada batasan minimal** - Tidak ada jarak waktu minimal antara masuk dan pulang
3. **Jam pulang hardcoded** - Tidak bisa dikonfigurasi oleh admin

## Solusi yang Diimplementasikan

### 1. Database Migration
- Menambahkan field `jam_pulang_minimal` (time) - Jam minimal untuk presensi pulang
- Menambahkan field `selisih_jam_minimal` (integer) - Minimal jam antara masuk dan pulang

### 2. Model Updates
- Update model `JamMasuk` untuk include field baru
- Field baru: `jam_pulang_minimal`, `selisih_jam_minimal`

### 3. Controller Updates

#### Guru/PresensiController.php
- Menambahkan validasi jam pulang minimal (default: 12:00)
- Menambahkan validasi selisih jam minimal (default: 4 jam)
- Pesan error yang informatif

#### Siswa/PresensiController.php
- Validasi yang sama seperti controller guru
- Konsistensi logika presensi

#### Admin/LiburController.php
- Menambahkan validasi untuk pengaturan jam pulang
- Validasi jam pulang minimal harus setelah jam tutup masuk
- Update method `updateJamMasuk` untuk handle field baru

### 4. View Updates
- Admin dapat mengatur:
  - Jam mulai masuk
  - Jam tutup masuk  
  - Jam pulang minimal
  - Selisih jam minimal
- Form validation dengan JavaScript
- Informasi pengaturan saat ini

### 5. Seeder Updates
- Update `JamMasukSeeder` dengan nilai default:
  - `jam_pulang_minimal`: 12:00
  - `selisih_jam_minimal`: 4 jam

## Cara Kerja

### Presensi Masuk
1. Siswa scan QR code untuk masuk
2. Sistem cek apakah sudah waktunya presensi (setelah jam mulai)
3. Status ditentukan berdasarkan waktu (tepat waktu/terlambat)

### Presensi Pulang
1. Siswa scan QR code untuk pulang
2. Sistem validasi:
   - Sudah presensi masuk hari ini
   - Belum presensi pulang
   - Sudah jam pulang minimal (default: 12:00)
   - Sudah minimal 4 jam sejak presensi masuk
3. Jika semua validasi passed, presensi pulang berhasil

## Pengaturan Default

- **Jam Mulai Masuk**: 07:30
- **Jam Tutup Masuk**: 08:30  
- **Jam Pulang Minimal**: 12:00
- **Selisih Jam Minimal**: 4 jam

## Manfaat

1. **Mencegah kesalahan** - Tidak bisa langsung pulang setelah masuk
2. **Fleksibel** - Admin bisa mengatur sesuai kebutuhan sekolah
3. **Konsisten** - Logika yang sama untuk guru dan siswa
4. **Informatif** - Pesan error yang jelas untuk user

## Cara Menggunakan

1. Login sebagai admin
2. Buka menu "Hari Libur"
3. Scroll ke bagian "Pengaturan Jam Presensi"
4. Atur jam sesuai kebutuhan sekolah
5. Klik "Update Pengaturan Jam Presensi"

## Validasi

- Jam tutup masuk harus setelah jam mulai
- Jam pulang minimal harus setelah jam tutup masuk
- Selisih jam minimal antara 1-12 jam
- Semua field wajib diisi
