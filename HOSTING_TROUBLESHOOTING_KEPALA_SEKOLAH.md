# 🔧 Troubleshooting Dashboard Kepala Sekolah di Hosting

## 📋 **Masalah yang Ditemukan**
Dashboard Kepala Sekolah tidak bisa diklik panelnya di hosting, muncul error 404 saat mengakses API endpoints.

## 🚀 **Solusi yang Telah Diterapkan**

### 1. **Fallback API Routes**
Menambahkan route alternatif di `routes/web.php`:
```php
// Fallback API routes for Kepala Sekolah (to fix hosting issues)
Route::get('/kepala-sekolah/api/siswa-alpa', [App\Http\Controllers\KepalaSekolah\DashboardController::class, 'getSiswaAlpa']);
Route::get('/kepala-sekolah/api/siswa-by-status/{status}', [App\Http\Controllers\KepalaSekolah\DashboardController::class, 'getSiswaByStatus']);
```

### 2. **JavaScript Fallback URLs**
Update JavaScript di `resources/views/kepala-sekolah/dashboard.blade.php` dengan multiple URL fallback:
```javascript
const urls = [
    '/kepala-sekolah/siswa-alpa',
    '/kepala-sekolah/api/siswa-alpa',
    window.location.pathname.replace('/kepala-sekolah', '') + '/kepala-sekolah/siswa-alpa',
    window.location.pathname.replace('/kepala-sekolah', '') + '/kepala-sekolah/api/siswa-alpa'
];
```

### 3. **Enhanced .htaccess Files**
- **Root `.htaccess`**: Handle subfolder routing
- **Public `.htaccess`**: Enhanced rewrite rules untuk hosting

### 4. **Debug File**
File `debug_hosting_kepala_sekolah.php` untuk testing langsung di hosting.

## 🔍 **Langkah Troubleshooting**

### **Step 1: Test Debug File**
1. Upload file `debug_hosting_kepala_sekolah.php` ke folder `public`
2. Akses: `bitubi.my.id/presensi_siswa/public/debug_hosting_kepala_sekolah.php`
3. Periksa output untuk identifikasi masalah

### **Step 2: Test API Endpoints Manual**
Test endpoint berikut satu per satu:
- `bitubi.my.id/presensi_siswa/public/kepala-sekolah/siswa-alpa`
- `bitubi.my.id/presensi_siswa/public/kepala-sekolah/api/siswa-alpa`
- `bitubi.my.id/presensi_siswa/public/kepala-sekolah/siswa-by-status/tepat_waktu`

### **Step 3: Periksa File .htaccess**
Pastikan file `.htaccess` sudah terupload dengan benar:
- Root folder: `.htaccess`
- Public folder: `public/.htaccess`

### **Step 4: Clear Cache**
Jalankan perintah berikut di hosting:
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
```

### **Step 5: Periksa Permission**
Pastikan permission folder sudah benar:
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 644 .env
```

## 🐛 **Kemungkinan Penyebab Error 404**

### 1. **Subfolder Routing**
- Hosting menggunakan subfolder `presensi_siswa`
- Laravel tidak bisa handle routing dengan benar

### 2. **.htaccess Issues**
- File `.htaccess` tidak terupload
- Rewrite rules tidak berfungsi
- Mod_rewrite tidak enabled

### 3. **Route Caching**
- Route cache masih menggunakan konfigurasi lama
- Perlu clear route cache

### 4. **Virtual Host Configuration**
- Hosting tidak dikonfigurasi untuk handle Laravel
- Document root tidak mengarah ke folder public

## ✅ **Solusi Alternatif**

### **Option 1: Update APP_URL di .env**
```env
APP_URL=https://bitubi.my.id/presensi_siswa/public
```

### **Option 2: Force HTTPS (jika hosting support)**
```env
FORCE_HTTPS=true
```

### **Option 3: Custom Base URL**
Tambahkan di `AppServiceProvider.php`:
```php
public function boot()
{
    if (request()->is('presensi_siswa/*')) {
        URL::forceRootUrl(config('app.url') . '/presensi_siswa/public');
    }
}
```

## 📱 **Testing di Hosting**

### **Test 1: Basic Access**
- ✅ Dashboard utama: `bitubi.my.id/presensi_siswa/public/kepala-sekolah`
- ❌ API endpoint: `bitubi.my.id/presensi_siswa/public/kepala-sekolah/siswa-alpa`

### **Test 2: Fallback URLs**
- ✅ Fallback API: `bitubi.my.id/presensi_siswa/public/kepala-sekolah/api/siswa-alpa`
- ✅ Alternative routing: `bitubi.my.id/presensi_siswa/public/kepala-sekolah/api/siswa-by-status/tepat_waktu`

### **Test 3: JavaScript Console**
Buka Developer Tools → Console, lihat error yang muncul saat klik panel.

## 🔧 **Perintah Hosting yang Perlu Dijalankan**

```bash
# Masuk ke folder aplikasi
cd /home/username/public_html/presensi_siswa

# Clear semua cache
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# Regenerate autoload
composer dump-autoload

# Test route list
php artisan route:list | grep kepala-sekolah
```

## 📞 **Kontak Support Hosting**

Jika masalah masih berlanjut, hubungi support hosting dengan informasi:
1. Error yang muncul (404)
2. URL yang gagal diakses
3. Struktur folder aplikasi
4. File `.htaccess` yang sudah dibuat

## 🎯 **Expected Result**

Setelah semua solusi diterapkan:
- ✅ Panel dashboard bisa diklik
- ✅ Modal data siswa muncul
- ✅ Tidak ada error 404
- ✅ API endpoints berfungsi normal

## 📝 **Catatan Penting**

- **Jangan hapus** file debug sampai masalah teratasi
- **Backup** file sebelum melakukan perubahan
- **Test** setiap perubahan secara bertahap
- **Monitor** log error di `storage/logs/laravel.log`

---

**Status:** 🔄 In Progress  
**Last Updated:** {{ date('Y-m-d H:i:s') }}  
**Next Action:** Test debug file di hosting
