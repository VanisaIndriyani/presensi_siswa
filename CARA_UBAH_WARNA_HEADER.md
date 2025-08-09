# Cara Mengubah Warna Header Tabel - Admin Presensi

## Warna Header Saat Ini
Header tabel saat ini menggunakan warna **Hijau** dengan gradient.

## Cara Mengubah Warna Header

### **Langkah 1: Buka File**
Buka file: `resources/views/admin/presensi/index.blade.php`

### **Langkah 2: Cari CSS Header**
Cari bagian CSS untuk header tabel (sekitar baris 30-40):

```css
/* Header tabel dengan warna background */
.table thead th {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important; /* Hijau */
    color: white !important;
    font-weight: 600;
    border: none;
    padding: 12px 8px;
    text-align: center;
    vertical-align: middle;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
```

### **Langkah 3: Pilih Warna yang Diinginkan**

#### **Option 1: Header Biru**
```css
.table thead th {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
    /* ... properti lainnya tetap sama ... */
}
```

#### **Option 2: Header Ungu**
```css
.table thead th {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%) !important;
    /* ... properti lainnya tetap sama ... */
}
```

#### **Option 3: Header Orange**
```css
.table thead th {
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%) !important;
    /* ... properti lainnya tetap sama ... */
}
```

#### **Option 4: Header Merah**
```css
.table thead th {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
    /* ... properti lainnya tetap sama ... */
}
```

#### **Option 5: Header Teal**
```css
.table thead th {
    background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%) !important;
    /* ... properti lainnya tetap sama ... */
}
```

#### **Option 6: Header Pink**
```css
.table thead th {
    background: linear-gradient(135deg, #ec4899 0%, #be185d 100%) !important;
    /* ... properti lainnya tetap sama ... */
}
```

#### **Option 7: Header Indigo**
```css
.table thead th {
    background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%) !important;
    /* ... properti lainnya tetap sama ... */
}
```

## Contoh Perubahan Lengkap

### **Untuk Header Biru:**
```css
/* Header tabel dengan warna background */
.table thead th {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important; /* Biru */
    color: white !important;
    font-weight: 600;
    border: none;
    padding: 12px 8px;
    text-align: center;
    vertical-align: middle;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
```

## Warna yang Tersedia

| Warna | Kode Gradient | Deskripsi |
|-------|---------------|-----------|
| 🟢 **Hijau** | `#059669` → `#10b981` | Default saat ini |
| 🔵 **Biru** | `#3b82f6` → `#1d4ed8` | Professional |
| 🟣 **Ungu** | `#7c3aed` → `#5b21b6` | Modern |
| 🟠 **Orange** | `#f97316` → `#ea580c` | Energetic |
| 🔴 **Merah** | `#ef4444` → `#dc2626` | Attention |
| 🔷 **Teal** | `#14b8a6` → `#0d9488` | Calm |
| 💗 **Pink** | `#ec4899` → `#be185d` | Playful |
| 🔷 **Indigo** | `#6366f1` → `#4338ca` | Trustworthy |

## Tips

### **1. Konsistensi Warna**
- Pilih warna yang sesuai dengan tema aplikasi
- Hijau cocok untuk aplikasi pendidikan
- Biru cocok untuk aplikasi profesional
- Ungu cocok untuk aplikasi modern

### **2. Kontras**
- Semua warna sudah dioptimalkan untuk kontras dengan teks putih
- Text shadow membantu keterbacaan

### **3. Gradient**
- Semua warna menggunakan gradient untuk tampilan yang lebih menarik
- Gradient dari gelap ke terang memberikan efek 3D

### **4. Efek Visual**
- Box shadow memberikan efek floating
- Border radius memberikan tampilan modern
- Text shadow meningkatkan keterbacaan

## Setelah Mengubah Warna

1. **Simpan file** `resources/views/admin/presensi/index.blade.php`
2. **Refresh browser** untuk melihat perubahan
3. **Clear cache** jika diperlukan:
   ```bash
   php artisan view:clear
   ```

## Troubleshooting

### **Warna Tidak Berubah**
1. Pastikan file tersimpan dengan benar
2. Clear browser cache (Ctrl+F5)
3. Clear Laravel view cache
4. Periksa syntax CSS

### **Warna Terlalu Gelap/Terang**
1. Sesuaikan kode warna gradient
2. Gunakan color picker untuk mendapatkan warna yang tepat
3. Test di browser untuk memastikan kontras yang baik
