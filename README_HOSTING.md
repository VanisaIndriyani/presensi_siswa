# Panduan Setup Setelah Hosting

## 1. Setup Database
```bash
# Jalankan migration
php artisan migrate

# Jalankan seeder untuk membuat user default
php artisan db:seed
```

## 2. User Default yang Dibuat

### Admin
- **Email**: admin@sekolah.com
- **Password**: admin123
- **Role**: admin

### Guru
- **Email**: guru@sekolah.com
- **Password**: guru123
- **Role**: guru

### Kepala Sekolah
- **Email**: kepalasekolah@gmail.com
- **Password**: kepalasekolah123
- **Role**: kepala_sekolah

## 3. Jika Login Kepala Sekolah Tidak Bisa

### Langkah 1: Jalankan Seeder Khusus
```bash
php artisan db:seed --class=KepalaSekolahSeeder
```

### Langkah 2: Cek Database
Pastikan tabel `users` memiliki kolom `role` dengan value `kepala_sekolah`

### Langkah 3: Buat User Manual (Jika Perlu)
```bash
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Kepala Sekolah',
    'email' => 'kepalasekolah@gmail.com',
    'password' => Hash::make('kepalasekolah123'),
    'role' => 'kepala_sekolah',
]);
```

## 4. Troubleshooting

### Error: "Column 'role' not found"
Jalankan migration untuk menambah kolom role:
```bash
php artisan migrate
```

### Error: "Table 'users' doesn't exist"
Jalankan semua migration:
```bash
php artisan migrate:fresh --seed
```

### Error: "Class 'KepalaSekolahSeeder' not found"
Pastikan file `database/seeders/KepalaSekolahSeeder.php` ada dan terdaftar di `DatabaseSeeder.php`

## 5. Cek Status User
```bash
php artisan tinker
```

```php
use App\Models\User;
User::all(['name', 'email', 'role']);
```

## 6. Reset Password (Jika Lupa)
```bash
php artisan tinker
```

```php
use App\Models\User;
$user = User::where('email', 'kepalasekolah@gmail.com')->first();
$user->password = Hash::make('kepalasekolah123');
$user->save();
```

## 7. File Konfigurasi Penting
- `.env` - Konfigurasi database dan aplikasi
- `config/auth.php` - Konfigurasi autentikasi
- `app/Http/Middleware/RoleMiddleware.php` - Middleware untuk role

## 8. URL Login
- **Login Page**: `yourdomain.com/login`
- **Admin Dashboard**: `yourdomain.com/admin`
- **Guru Dashboard**: `yourdomain.com/guru`
- **Kepala Sekolah Dashboard**: `yourdomain.com/kepala-sekolah` 