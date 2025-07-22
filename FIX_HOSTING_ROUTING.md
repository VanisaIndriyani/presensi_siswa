# 🔧 PERBAIKAN ROUTING HOSTING - SISTEM PRESENSI SISWA

## 📋 **Masalah yang Ditemukan:**
- Error 404 saat klik kartu di dashboard guru
- URL yang diakses: `bitubi.my.id/presensi_siswa/public/index.php/guru`
- Seharusnya: `bitubi.my.id/presensi_siswa/public/guru`

## 🛠️ **Solusi yang Diterapkan:**

### **1. File .htaccess di Root Folder**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### **2. Perbaikan URL di JavaScript**
**File:** `resources/views/guru/dashboard.blade.php`

**Sebelum:**
```javascript
fetch(`/api/siswa-by-status/${status}`)
fetch(`/guru/presensi/${id}/edit`)
```

**Sesudah:**
```javascript
fetch(`{{ url('/api/siswa-by-status') }}/${status}`)
fetch(`{{ url('/guru/presensi') }}/${id}/edit`)
```

### **3. Route API Baru**
**File:** `routes/web.php`
```php
Route::get('/api/siswa-by-status/{status}', function($status) {
    // Logic untuk mengambil data siswa berdasarkan status
});
```

## 📁 **File yang Perlu Diupload ke Hosting:**

1. **`.htaccess`** (di root folder)
2. **`routes/web.php`**
3. **`resources/views/guru/dashboard.blade.php`**
4. **`test_urls.php`** (untuk testing)

## 🚀 **Langkah-langkah di Hosting:**

### **Step 1: Upload File**
```bash
# Upload semua file yang sudah diubah
```

### **Step 2: Clear Cache**
```bash
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

### **Step 3: Test Routing**
```bash
php test_urls.php
```

### **Step 4: Cek .htaccess**
Pastikan file `.htaccess` ada di:
- Root folder: `/presensi_siswa/.htaccess`
- Public folder: `/presensi_siswa/public/.htaccess`

### **Step 5: Test di Browser**
1. Buka: `https://bitubi.my.id/presensi_siswa/public/guru`
2. Login sebagai guru
3. Klik kartu "Tepat Waktu", "Terlambat", atau "Absen"
4. Modal seharusnya muncul dengan data siswa

## 🔍 **Troubleshooting:**

### **Jika Masih Error 404:**

#### **A. Cek .htaccess**
```bash
# Pastikan file .htaccess ada
ls -la .htaccess
ls -la public/.htaccess
```

#### **B. Cek mod_rewrite**
```bash
# Test apakah mod_rewrite aktif
php -m | grep rewrite
```

#### **C. Cek URL di Browser**
Buka langsung di browser:
- `https://bitubi.my.id/presensi_siswa/public/api/siswa-by-status/tepat_waktu`
- Seharusnya return JSON data

#### **D. Cek Error Log**
```bash
tail -f storage/logs/laravel.log
```

### **Jika .htaccess Tidak Berfungsi:**

#### **Alternatif 1: Gunakan index.php**
Ubah semua URL di JavaScript menjadi:
```javascript
fetch(`{{ url('/index.php/api/siswa-by-status') }}/${status}`)
```

#### **Alternatif 2: Konfigurasi Virtual Host**
Jika punya akses ke server, set document root ke folder `public/`

## ✅ **Verifikasi Berhasil:**

1. **URL di browser:** `bitubi.my.id/presensi_siswa/public/guru` (tanpa index.php)
2. **Klik kartu dashboard:** Modal muncul dengan data siswa
3. **Console browser:** Tidak ada error 404
4. **Network tab:** Request ke `/api/siswa-by-status/tepat_waktu` berhasil (200)

## 📞 **Jika Masih Bermasalah:**

1. Jalankan `php test_urls.php` dan kirim hasilnya
2. Cek error log hosting
3. Pastikan mod_rewrite aktif di hosting
4. Coba akses URL API langsung di browser 