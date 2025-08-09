# Fitur Pencarian Siswa - Admin Presensi

## Fitur yang Ditambahkan

### 1. **Pencarian Real-time**
- ✅ Input pencarian dengan icon search
- ✅ Auto-submit setelah 500ms delay
- ✅ Pencarian berdasarkan nama atau NISN siswa
- ✅ Highlight hasil pencarian di tabel

### 2. **UI/UX Improvements**
- ✅ Form filter yang responsif
- ✅ Badge filter aktif
- ✅ Tombol reset untuk clear semua filter
- ✅ Keyboard shortcuts (Ctrl+F untuk fokus)
- ✅ Tombol clear search (X) ketika ada pencarian

### 3. **Fitur Tambahan**
- ✅ Highlight text yang cocok dengan pencarian
- ✅ Row highlighting untuk hasil pencarian
- ✅ Informasi total data yang ditemukan
- ✅ Responsive design untuk mobile

## Cara Menggunakan

### **Pencarian Dasar**
1. Masuk ke menu **Admin > Presensi**
2. Ketik nama atau NISN siswa di kolom "Cari Siswa"
3. Sistem akan otomatis mencari setelah 500ms
4. Hasil pencarian akan di-highlight

### **Filter Kombinasi**
- **Pencarian + Kelas**: Cari siswa tertentu di kelas tertentu
- **Pencarian + Periode**: Cari siswa dalam periode tertentu
- **Semua Filter**: Kombinasikan pencarian, kelas, dan periode

### **Keyboard Shortcuts**
- **Ctrl+F**: Fokus ke kolom pencarian
- **Escape**: Clear pencarian dan refresh
- **Enter**: Submit form pencarian

### **Tombol Aksi**
- **Filter**: Terapkan semua filter yang dipilih
- **Reset**: Clear semua filter dan kembali ke tampilan awal
- **X**: Clear hanya pencarian (ketika ada text)

## Fitur Teknis

### **Backend (Controller)**
```php
// Pencarian berdasarkan nama atau NISN
if ($request->search) {
    $search = $request->search;
    $query->whereHas('siswa', function($q) use ($search) {
        $q->where('nama', 'like', "%$search%")
          ->orWhere('nisn', 'like', "%$search%");
    });
}
```

### **Frontend (JavaScript)**
```javascript
// Real-time search dengan debounce
let searchTimeout;
searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        document.getElementById('filterForm').submit();
    }, 500);
});
```

### **Highlight Results**
```php
// Highlight text yang cocok
@if(request('search') && stripos($p->siswa->nama ?? '', request('search')) !== false)
    {!! str_ireplace(request('search'), '<mark>' . request('search') . '</mark>', $p->siswa->nama ?? '-') !!}
@else
    {{ $p->siswa->nama ?? '-' }}
@endif
```

## Tampilan Visual

### **Form Filter**
```
┌─────────────────────────────────────────────────────────────┐
│ 🔍 Cari Siswa: [Nama atau NISN siswa...] [X]              │
│ 📚 Filter Kelas: [Dropdown]                                │
│ 📅 Filter Bulan: [Dropdown]                                │
│ 📆 Tahun: [Dropdown]                                       │
│ [🔍 Filter] [🔄 Reset]                                     │
└─────────────────────────────────────────────────────────────┘
```

### **Filter Aktif**
```
ℹ️ Filter Aktif: 
[🔍 Pencarian: "John"] [📚 Kelas: X-A] [📅 Periode: Januari 2024] [📊 Total: 15 data]
```

### **Hasil Pencarian**
```
┌─────────────────────────────────────────────────────────────┐
│ No │ Nama Siswa    │ NISN      │ Kelas │ Status           │
├────┼───────────────┼───────────┼───────┼──────────────────┤
│ 1  │ <mark>John</mark> Doe │ 1234567890 │ X-A   │ Tepat Waktu      │ ← Highlighted
│ 2  │ Jane Smith    │ 0987654321 │ X-B   │ Terlambat       │
└─────────────────────────────────────────────────────────────┘
```

## Responsive Design

### **Desktop (>768px)**
- Form filter horizontal
- Input group dengan icon
- Semua filter dalam satu baris

### **Mobile (≤768px)**
- Form filter vertical
- Input group full width
- Filter stack vertically

## Performance

### **Optimizations**
- ✅ Debounce search (500ms delay)
- ✅ Server-side filtering
- ✅ Efficient database queries
- ✅ Minimal DOM manipulation

### **Database Query**
```sql
SELECT * FROM presensis 
JOIN siswas ON presensis.siswa_id = siswas.id 
WHERE (siswas.nama LIKE '%search%' OR siswas.nisn LIKE '%search%')
AND siswas.kelas = 'X-A' 
AND MONTH(presensis.tanggal) = 1 
AND YEAR(presensis.tanggal) = 2024
ORDER BY presensis.waktu_scan DESC
```

## Manfaat

### **Untuk Admin**
1. **Cepat**: Pencarian real-time tanpa reload
2. **Akurat**: Filter kombinasi untuk hasil tepat
3. **Mudah**: Keyboard shortcuts dan UI intuitif
4. **Efisien**: Highlight hasil untuk identifikasi cepat

### **Untuk Sistem**
1. **Scalable**: Query yang efisien untuk data besar
2. **Responsive**: Bekerja baik di desktop dan mobile
3. **Accessible**: Keyboard navigation support
4. **Maintainable**: Code yang clean dan terstruktur

## Troubleshooting

### **Pencarian Tidak Berfungsi**
1. Pastikan JavaScript enabled
2. Check browser console untuk error
3. Verify database connection
4. Clear browser cache

### **Hasil Tidak Muncul**
1. Periksa spelling nama/NISN
2. Pastikan data ada di database
3. Check filter kombinasi
4. Verify date format

### **Performance Issues**
1. Reduce search delay (500ms → 300ms)
2. Add database indexes
3. Implement pagination
4. Cache frequent searches
