@extends('layouts.admin')

@section('content')
<style>
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
    .btn-primary {
        background: #7C3AED !important;
        border-color: #7C3AED !important;
    }
    .btn-primary:hover {
        background: #6D28D9 !important;
        border-color: #6D28D9 !important;
    }
    .alert-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        border: none !important;
        color: white !important;
    }
    .alert-info small {
        color: rgba(255, 255, 255, 0.8) !important;
    }
</style>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 page-title">Pengaturan Waktu & Hari Libur</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Hari Libur</button>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Pengaturan Jam Masuk -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="mb-3" style="color: #3B82F6; font-weight: 600;">
            <i class="fas fa-clock me-2"></i>Pengaturan Jam Masuk
        </h5>
        @if($jamMasuk)
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Jam Masuk Saat Ini:</strong> {{ $jamMasuk->start_time }} - {{ $jamMasuk->end_time }}
                <br><small class="text-muted">Siswa dapat presensi tepat waktu jika masuk antara jam {{ $jamMasuk->start_time }} - {{ $jamMasuk->end_time }}</small>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.libur.updateJamMasuk') }}">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Jam Mulai Masuk</label>
                        <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time', $jamMasuk->start_time ?? '07:00') }}" required>
                        <small class="text-muted">Jam mulai siswa bisa presensi</small>
                        @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Jam Tutup Masuk</label>
                        <input type="time" name="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time', $jamMasuk->end_time ?? '08:30') }}" required>
                        <small class="text-muted">Batas waktu presensi tepat waktu</small>
                        @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update Jam Masuk
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="mb-3">Daftar Hari Libur</h5>
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($liburs as $i => $libur)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $libur->tanggal }}</td>
                            <td>{{ $libur->keterangan }}</td>
                            <td>
                                <button class="btn btn-sm btn-info text-white btn-show" data-id="{{ $libur->id }}">Lihat</button>
                                <button class="btn btn-sm btn-warning btn-edit" data-id="{{ $libur->id }}">Edit</button>
                                <button class="btn btn-sm btn-danger btn-hapus" data-id="{{ $libur->id }}" data-tanggal="{{ $libur->tanggal }}">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Belum ada data hari libur.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Hari Libur -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('admin.libur.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="modalTambahLabel">Tambah Hari Libur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Tanggal</label>
          <input type="date" name="tanggal" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Keterangan</label>
          <input type="text" name="keterangan" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Hari Libur -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" id="formEdit">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditLabel">Edit Hari Libur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="edit-id">
        <div class="mb-3">
          <label class="form-label">Tanggal</label>
          <input type="date" name="tanggal" id="edit-tanggal" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Keterangan</label>
          <input type="text" name="keterangan" id="edit-keterangan" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Show Hari Libur -->
<div class="modal fade" id="modalShow" tabindex="-1" aria-labelledby="modalShowLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalShowLabel">Detail Hari Libur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group mb-3">
          <li class="list-group-item"><b>Tanggal:</b> <span id="show-tanggal"></span></li>
          <li class="list-group-item"><b>Keterangan:</b> <span id="show-keterangan"></span></li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Hapus Hari Libur -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" id="formHapus">
      @csrf
      @method('DELETE')
      <div class="modal-header">
        <h5 class="modal-title" id="modalHapusLabel">Hapus Hari Libur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Yakin ingin menghapus hari libur <b id="hapus-tanggal"></b>?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger">Hapus</button>
      </div>
    </form>
  </div>
</div>

<script>
const liburBaseUrl = "{{ url('admin/libur') }}";
// Show detail hari libur
const showBtns = document.querySelectorAll('.btn-show');
showBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        fetch(`${liburBaseUrl}/${btn.dataset.id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('show-tanggal').textContent = data.tanggal;
                document.getElementById('show-keterangan').textContent = data.keterangan;
                new bootstrap.Modal(document.getElementById('modalShow')).show();
            });
    });
});
// Edit hari libur
const editBtns = document.querySelectorAll('.btn-edit');
editBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        fetch(`${liburBaseUrl}/${btn.dataset.id}/edit`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('edit-id').value = data.id;
                document.getElementById('edit-tanggal').value = data.tanggal;
                document.getElementById('edit-keterangan').value = data.keterangan;
                document.getElementById('formEdit').action = `${liburBaseUrl}/${data.id}`;
                new bootstrap.Modal(document.getElementById('modalEdit')).show();
            });
    });
});
// Hapus hari libur
const hapusBtns = document.querySelectorAll('.btn-hapus');
hapusBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('hapus-tanggal').textContent = btn.dataset.tanggal;
        document.getElementById('formHapus').action = `${liburBaseUrl}/${btn.dataset.id}`;
        new bootstrap.Modal(document.getElementById('modalHapus')).show();
    });
});

// Validasi form jam masuk
document.querySelector('form[action*="jam-masuk"]').addEventListener('submit', function(e) {
    const startTime = document.querySelector('input[name="start_time"]').value;
    const endTime = document.querySelector('input[name="end_time"]').value;
    
    if (startTime && endTime && startTime >= endTime) {
        e.preventDefault();
        alert('Jam tutup harus setelah jam mulai!');
        return false;
    }
});

// Auto-update jam tutup saat jam mulai berubah
document.querySelector('input[name="start_time"]').addEventListener('change', function() {
    const startTime = this.value;
    const endTimeInput = document.querySelector('input[name="end_time"]');
    
    if (startTime) {
        // Set jam tutup 1 jam setelah jam mulai
        const startDate = new Date(`2000-01-01T${startTime}`);
        startDate.setHours(startDate.getHours() + 1);
        const suggestedEndTime = startDate.toTimeString().slice(0, 5);
        
        if (!endTimeInput.value || endTimeInput.value <= startTime) {
            endTimeInput.value = suggestedEndTime;
        }
    }
});
</script>
@endsection 