@extends('layouts.admin')

@section('content')
<style>
    .aksi-btn-group {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }
    .aksi-btn {
        border: none;
        border-radius: 10px;
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        transition: box-shadow 0.2s, transform 0.2s;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }
    .aksi-btn:active { transform: scale(0.96);}
    .aksi-btn.info { background: #22d3ee; color: #fff; }
    .aksi-btn.warning { background: #facc15; color: #fff; }
    .aksi-btn.danger { background: #ef4444; color: #fff; }
    .aksi-btn.info:hover { background: #06b6d4; }
    .aksi-btn.warning:hover { background: #eab308; }
    .aksi-btn.danger:hover { background: #dc2626; }
    .aksi-btn i { font-size: 1.15rem; }
    .table td, .table th { vertical-align: middle !important; }
    
    /* Warna teks yang lebih lembut */
    .page-title {
        color: #7C3AED !important; /* Ungu untuk judul */
        font-weight: 700;
    }
    .table th {
        color: #3B82F6 !important; /* Biru untuk header tabel */
        font-weight: 600;
    }
    .table td {
        color: #4B5563 !important; /* Abu-abu medium untuk isi tabel */
    }
    .form-label {
        color: #7C3AED !important; /* Ungu untuk label */
        font-weight: 500;
    }
    .form-control::placeholder {
        color: #9CA3AF !important; /* Abu-abu untuk placeholder */
    }
</style>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 page-title">Data Presensi Siswa</h1>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form id="filterForm" method="GET" class="d-flex align-items-center mb-3" action="">
    <div class="me-2">
        <label for="filterKelas" class="form-label mb-0">Filter Kelas:</label>
        <select name="kelas" id="filterKelas" class="form-select" style="width:auto; display:inline-block;">
            <option value="">Semua Kelas</option>
            @foreach($presensis->pluck('siswa.kelas')->unique()->filter() as $kelas)
                <option value="{{ $kelas }}" {{ request('kelas') == $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
            @endforeach
        </select>
    </div>
    <div class="me-2">
        <label for="filterBulan" class="form-label mb-0">Filter Bulan:</label>
        <select name="bulan" id="filterBulan" class="form-select" style="width:auto; display:inline-block;">
            <option value="">Semua Bulan</option>
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ sprintf('%02d', $m) }}" {{ request('bulan', now()->format('m')) == sprintf('%02d', $m) ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
            @endfor
        </select>
    </div>
    <div class="me-2">
        <label for="filterTahun" class="form-label mb-0">Tahun:</label>
        <select name="tahun" id="filterTahun" class="form-select" style="width:auto; display:inline-block;">
            @for($y = now()->year; $y >= now()->year - 5; $y--)
                <option value="{{ $y }}" {{ request('tahun', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </div>
    <button type="submit" class="btn btn-primary ms-2">Filter</button>
</form>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Kelas</th>
                        <th>jam masuk </th>
                        <th>Jam Pulang</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($presensis as $i => $p)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $p->siswa->nama ?? '-' }}</td>
                            <td>{{ $p->siswa->nisn ?? '-' }}</td>
                            <td>{{ $p->siswa->kelas ?? '-' }}</td>
                            <td>{{ $p->waktu_scan }}</td>
                            <td>{{ $p->jam_pulang ? $p->jam_pulang : '-' }}</td>
                            <td>
                                @if($p->status == 'tepat_waktu')
                                    <span class="badge bg-success">Tepat Waktu</span>
                                @elseif($p->status == 'terlambat')
                                    <span class="badge bg-danger">Terlambat</span>
                                @elseif($p->status == 'sakit')
                                    <span class="badge bg-warning text-dark">Sakit</span>
                                @elseif($p->status == 'izin')
                                    <span class="badge bg-info text-dark">Izin</span>
                                @elseif($p->status == 'alpa')
<span class="badge bg-secondary">Alpa</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $jam = isset($p->waktu_scan) ? \Carbon\Carbon::parse($p->waktu_scan)->format('H:i') : '-';
                                @endphp
                                @if($p->status === 'terlambat')
                                    Datang pukul {{ $jam }}, melewati jam masuk 07:30
                                @elseif($p->status === 'tepat_waktu')
                                    Datang pukul {{ $jam }}, sesuai waktu kedatangan
                                @elseif($p->status === 'izin')
                                    {{ $p->keterangan ?? '-' }}
                                @elseif($p->status === 'sakit')
                                    Izin sakit, surat diserahkan ke TU
                                @elseif($p->status === 'alpa')
                                    Tidak hadir tanpa keterangan
                                @else
                                    {{ $p->keterangan ?? '-' }}
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="aksi-btn-group">
                                    <button class="aksi-btn info btn-show" data-id="{{ $p->id }}" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="aksi-btn warning btn-edit" data-id="{{ $p->id }}" title="Edit">
                                        <i class="fas fa-pen-to-square"></i>
                                    </button>
                                    <button class="aksi-btn danger btn-hapus" data-id="{{ $p->id }}" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada data presensi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Show Presensi -->
<div class="modal fade" id="modalShow" tabindex="-1" aria-labelledby="modalShowLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalShowLabel">Detail Presensi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group mb-3">
          <li class="list-group-item"><b>Nama Siswa:</b> <span id="show-nama"></span></li>
          <li class="list-group-item"><b>NISN:</b> <span id="show-nisn"></span></li>
          <li class="list-group-item"><b>Kelas:</b> <span id="show-kelas"></span></li>
          <li class="list-group-item"><b>Waktu Scan:</b> <span id="show-waktu_scan"></span></li>
          <li class="list-group-item"><b>Status:</b> <span id="show-status"></span></li>
          <li class="list-group-item"><b>Keterangan:</b> <span id="show-keterangan"></span></li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit Presensi -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" id="formEdit">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditLabel">Edit Presensi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Status</label>
          <select name="status" id="edit-status" class="form-select" required>
            <option value="tepat_waktu">Tepat Waktu</option>
            <option value="terlambat">Terlambat</option>
            <option value="sakit">Sakit</option>
            <option value="izin">Izin</option>
            <option value="alpa">Alpa</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Keterangan</label>
          <input type="text" name="keterangan" id="edit-keterangan" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Hapus Presensi -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" id="formHapus">
      @csrf
      @method('DELETE')
      <div class="modal-header">
        <h5 class="modal-title" id="modalHapusLabel">Hapus Presensi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Yakin ingin menghapus data presensi ini?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger">Hapus</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const presensiBaseUrl = "{{ url('admin/presensi') }}";
    
    // Function to attach action events
    function attachActionEvents() {
        // Show detail presensi
        document.querySelectorAll('.btn-show').forEach(btn => {
            btn.addEventListener('click', function() {
                fetch(`${presensiBaseUrl}/${btn.dataset.id}`)
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('show-nama').textContent = data.siswa?.nama || '-';
                        document.getElementById('show-nisn').textContent = data.siswa?.nisn || '-';
                        document.getElementById('show-kelas').textContent = data.siswa?.kelas || '-';
                        document.getElementById('show-waktu_scan').textContent = data.waktu_scan;
                        document.getElementById('show-status').textContent = data.status === 'tepat_waktu' ? 'Tepat Waktu' : 
                            (data.status === 'terlambat' ? 'Terlambat' : 
                            (data.status === 'sakit' ? 'Sakit' : 
                            (data.status === 'izin' ? 'Izin' : 
                            (data.status === 'alpa' ? 'Alpa' : data.status))));
                        
                        // Generate keterangan yang sama seperti di tabel
                        let keterangan = '';
                        const jam = data.waktu_scan ? new Date(data.waktu_scan).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}) : '-';
                        
                        if (data.status === 'terlambat') {
                            keterangan = `Datang pukul ${jam}, melewati jam masuk 07:30`;
                        } else if (data.status === 'tepat_waktu') {
                            keterangan = `Datang pukul ${jam}, sesuai waktu kedatangan`;
                        } else if (data.status === 'izin') {
                            keterangan = data.keterangan || '-';
                        } else if (data.status === 'sakit') {
                            keterangan = 'Izin sakit, surat diserahkan ke TU';
                        } else if (data.status === 'alpa') {
                            keterangan = 'Tidak hadir tanpa keterangan';
                        } else {
                            keterangan = data.keterangan || '-';
                        }
                        
                        document.getElementById('show-keterangan').textContent = keterangan;
                        new bootstrap.Modal(document.getElementById('modalShow')).show();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Gagal memuat data presensi');
                    });
            });
        });
        
        // Edit presensi
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                fetch(`${presensiBaseUrl}/${btn.dataset.id}/edit`)
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('edit-status').value = data.status;
                        
                        // Generate keterangan yang sama seperti di tabel
                        let keterangan = '';
                        const jam = data.waktu_scan ? new Date(data.waktu_scan).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}) : '-';
                        
                        if (data.status === 'terlambat') {
                            keterangan = `Datang pukul ${jam}, melewati jam masuk 07:30`;
                        } else if (data.status === 'tepat_waktu') {
                            keterangan = `Datang pukul ${jam}, sesuai waktu kedatangan`;
                        } else if (data.status === 'izin') {
                            keterangan = data.keterangan || '';
                        } else if (data.status === 'sakit') {
                            keterangan = 'Izin sakit, surat diserahkan ke TU';
                        } else if (data.status === 'alpa') {
                            keterangan = 'Tidak hadir tanpa keterangan';
                        } else {
                            keterangan = data.keterangan || '';
                        }
                        
                        document.getElementById('edit-keterangan').value = keterangan;
                        document.getElementById('formEdit').action = `${presensiBaseUrl}/${btn.dataset.id}`;
                        
                        // Store waktu_scan for auto-update feature
                        const formEdit = document.getElementById('formEdit');
                        formEdit.setAttribute('data-waktu-scan', data.waktu_scan);
                        
                        new bootstrap.Modal(document.getElementById('modalEdit')).show();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Gagal memuat data presensi untuk edit');
                    });
            });
        });
        
        // Hapus presensi
        document.querySelectorAll('.btn-hapus').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('formHapus').action = `${presensiBaseUrl}/${btn.dataset.id}`;
                new bootstrap.Modal(document.getElementById('modalHapus')).show();
            });
        });
    }
    
    // Attach events on page load
    attachActionEvents();
    
    // Auto-update keterangan when status changes in edit modal
    document.getElementById('edit-status').addEventListener('change', function() {
        const status = this.value;
        const keteranganField = document.getElementById('edit-keterangan');
        const formEdit = document.getElementById('formEdit');
        const waktuScan = formEdit.getAttribute('data-waktu-scan');
        
        let keterangan = '';
        const jam = waktuScan ? new Date(waktuScan).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}) : '-';
        
        if (status === 'terlambat') {
            keterangan = `Datang pukul ${jam}, melewati jam masuk 07:30`;
        } else if (status === 'tepat_waktu') {
            keterangan = `Datang pukul ${jam}, sesuai waktu kedatangan`;
        } else if (status === 'izin') {
            keterangan = keteranganField.value || '';
        } else if (status === 'sakit') {
            keterangan = 'Izin sakit, surat diserahkan ke TU';
        } else if (status === 'alpa') {
            keterangan = 'Tidak hadir tanpa keterangan';
        } else {
            keterangan = keteranganField.value || '';
        }
        
        keteranganField.value = keterangan;
    });
    
    // Filter form submission
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        // Form will submit normally
    });
});
</script>
@endsection 