# Troubleshooting Panel Kepala Sekolah di Hosting

## Masalah
Panel kepala sekolah tidak menampilkan data siswa ketika diklik di hosting, padahal di local bisa.

## Penyebab Umum

### 1. **Masalah Routing**
- Hosting mungkin memiliki konfigurasi routing yang berbeda
- URL rewriting tidak berfungsi dengan baik
- Case sensitivity pada nama file/folder

### 2. **Masalah Database**
- Koneksi database berbeda antara local dan hosting
- Timezone database berbeda
- Permission database berbeda

### 3. **Masalah Environment**
- File `.env` tidak ter-update di hosting
- Cache Laravel belum di-clear
- Permission file/folder tidak tepat

## Solusi yang Sudah Diimplementasikan

### 1. **Error Handling yang Lebih Baik**
- ✅ Menambahkan try-catch di controller
- ✅ Logging error untuk debugging
- ✅ Response error yang informatif

### 2. **JavaScript Error Handling**
- ✅ Validasi response HTTP status
- ✅ Handling error dari server
- ✅ Pesan error yang lebih detail

### 3. **Data Validation**
- ✅ Null safety untuk data siswa
- ✅ Fallback value untuk field kosong
- ✅ Validasi data sebelum response

## Langkah Troubleshooting

### Step 1: Test Debug File
1. Upload file `debug_kepala_sekolah.php` ke hosting
2. Akses: `yourdomain.com/debug_kepala_sekolah.php`
3. Periksa output untuk mengidentifikasi masalah

### Step 2: Test API Endpoints Manual
1. Test endpoint langsung di browser:
   - `yourdomain.com/kepala-sekolah/siswa-alpa`
   - `yourdomain.com/kepala-sekolah/siswa-by-status/total`
   - `yourdomain.com/kepala-sekolah/siswa-by-status/tepat_waktu`

### Step 3: Clear Laravel Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Step 4: Check Environment
1. Pastikan file `.env` ter-update di hosting
2. Periksa konfigurasi database
3. Pastikan timezone sesuai

### Step 5: Check File Permissions
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## Debug Checklist

### ✅ Database Connection
- [ ] Database bisa diakses
- [ ] Tabel ada dan berisi data
- [ ] Query berjalan tanpa error

### ✅ Models
- [ ] Model Siswa bisa diakses
- [ ] Model Presensi bisa diakses
- [ ] Relationship berfungsi

### ✅ Routes
- [ ] Route terdaftar dengan benar
- [ ] Middleware berfungsi
- [ ] URL bisa diakses

### ✅ Controller
- [ ] Method bisa diakses
- [ ] Data query berhasil
- [ ] Response format benar

### ✅ JavaScript
- [ ] Fetch request berhasil
- [ ] Response parsing berhasil
- [ ] DOM manipulation berhasil

## Common Issues & Solutions

### Issue 1: 404 Not Found
**Solution:**
- Periksa file `.htaccess`
- Pastikan mod_rewrite enabled
- Check URL rewriting di hosting

### Issue 2: 500 Internal Server Error
**Solution:**
- Periksa error log hosting
- Pastikan PHP version compatible
- Check file permissions

### Issue 3: Empty Response
**Solution:**
- Periksa database connection
- Pastikan data ada di database
- Check query logic

### Issue 4: CORS Error
**Solution:**
- Tambahkan CORS headers
- Periksa domain configuration
- Check browser console

## Testing Commands

### Test Database
```bash
php artisan tinker
>>> App\Models\Siswa::count()
>>> App\Models\Presensi::whereDate('tanggal', today())->count()
```

### Test Routes
```bash
php artisan route:list | grep kepala-sekolah
```

### Test Environment
```bash
php artisan env
```

## Monitoring

### Log Files
- Check `storage/logs/laravel.log`
- Check hosting error logs
- Monitor browser console

### Performance
- Monitor response time
- Check memory usage
- Monitor database queries

## Prevention

### Best Practices
1. **Always test in staging environment first**
2. **Use environment-specific configurations**
3. **Implement proper error handling**
4. **Monitor logs regularly**
5. **Keep backups of working versions**

### Deployment Checklist
- [ ] Clear all caches
- [ ] Update environment variables
- [ ] Check file permissions
- [ ] Test all critical functions
- [ ] Monitor error logs

## Support

Jika masalah masih berlanjut:
1. Gunakan debug file untuk identifikasi masalah
2. Periksa error log hosting
3. Test endpoint manual
4. Bandingkan dengan environment local
5. Hubungi support hosting jika diperlukan
