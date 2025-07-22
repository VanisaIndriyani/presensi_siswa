@extends('layouts.guru')

@section('title', 'Presensi')

@section('content')
<style>
    /* Warna teks yang konsisten */
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
    
    /* Perbaikan tombol aksi */
    .btn-show, .btn-edit, .btn-delete {
        z-index: 10 !important;
        pointer-events: auto !important;
        position: relative !important;
    }
    .btn-show:hover, .btn-edit:hover, .btn-delete:hover {
        transform: scale(1.1);
        transition: transform 0.2s;
    }
</style>
<div class="row">
    <!-- Filter -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label">Pilih Tanggal</label>
                        <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control" onchange="this.form.submit()">
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label">Pilih Kelas</label>
                        <select name="kelas" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas }}" {{ request('kelas') == $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i>Filter
                        </button>
                        <a href="{{ route('guru.presensi.index') }}" class="btn btn-secondary">
                            <i class="fas fa-refresh me-1"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Daftar Presensi -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                <h5 class="card-title mb-2 mb-md-0 page-title">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Daftar Presensi - {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
                </h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('guru.presensi.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Tambah Presensi
                    </a>
                    <span class="badge bg-success">{{ $presensis->where('status', 'tepat_waktu')->count() }} Tepat Waktu</span>
                    <span class="badge bg-warning text-dark">{{ $presensis->where('status', 'terlambat')->count() }} Terlambat</span>
                </div>
            </div>
            <div class="card-body">
                @if(true)
                    @if($presensis->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th>NISN</th>
                                        <th>Kelas</th>
                                        <th>Jam Masuk</th>
                                        <th>Jam Pulang</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($presensis as $index => $presensi)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td><strong>{{ $presensi->siswa->nama }}</strong></td>
                                            <td>{{ $presensi->siswa->nisn }}</td>
                                            <td>{{ $presensi->siswa->kelas }}</td>
                                            <td><span class="text-success"><i class="fas fa-clock me-1"></i>{{ \Carbon\Carbon::parse($presensi->waktu_scan)->format('H:i:s') }}</span></td>
                                            <td>
                                                @if($presensi->jam_pulang)
                                                    <span class="text-primary"><i class="fas fa-clock me-1"></i>{{ \Carbon\Carbon::parse($presensi->jam_pulang)->format('H:i:s') }}</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($presensi->status === 'tepat_waktu')
                                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>Tepat Waktu</span>
                                                @elseif($presensi->status === 'terlambat')
                                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Terlambat</span>
                                                @elseif($presensi->status === 'izin')
                                                    <span class="badge bg-info text-white"><i class="fas fa-info-circle me-1"></i>Izin</span>
                                                @elseif($presensi->status === 'sakit')
                                                    <span class="badge bg-danger"><i class="fas fa-medkit me-1"></i>Sakit</span>
                                                @elseif($presensi->status === 'alpa')
<span class="badge bg-secondary"><i class="fas fa-times me-1"></i>Alpa</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $jam = isset($presensi->waktu_scan) ? \Carbon\Carbon::parse($presensi->waktu_scan)->format('H:i') : '-';
                                                @endphp
                                                @if($presensi->status === 'terlambat')
                                                    Datang pukul {{ $jam }}, melewati jam masuk 07:30
                                                @elseif($presensi->status === 'tepat_waktu')
                                                    Datang pukul {{ $jam }}, sesuai waktu kedatangan
                                                @elseif($presensi->status === 'izin')
                                                    {{ $presensi->keterangan ?? '-' }}
                                                @elseif($presensi->status === 'sakit')
                                                    Izin sakit, surat diserahkan ke TU
                                                @elseif($presensi->status === 'alpa')
                                                    Tidak hadir tanpa keterangan
                                                @else
                                                    {{ $presensi->keterangan ?? '-' }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-info text-white btn-show" data-id="{{ $presensi->id }}"><i class="fas fa-eye"></i></button>
                                                <button class="btn btn-sm btn-warning text-white btn-edit" data-id="{{ $presensi->id }}"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $presensi->id }}" data-nama="{{ $presensi->siswa->nama }}"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada presensi</h5>
                            <p class="text-muted">Tidak ada data presensi untuk tanggal yang dipilih</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

<!-- === MODAL SHOW === -->
<div class="modal fade" id="modalShow" tabindex="-1" aria-labelledby="modalShowLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Detail Presensi</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><b>Nama:</b> <span id="show-nama"></span></li>
          <li class="list-group-item"><b>NISN:</b> <span id="show-nisn"></span></li>
          <li class="list-group-item"><b>Kelas:</b> <span id="show-kelas"></span></li>
          <li class="list-group-item"><b>Tanggal:</b> <span id="show-tanggal"></span></li>
          <li class="list-group-item"><b>Waktu Scan:</b> <span id="show-waktu_scan"></span></li>
          <li class="list-group-item"><b>Status:</b> <span id="show-status"></span></li>
          <li class="list-group-item"><b>Keterangan:</b> <span id="show-keterangan"></span></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- === MODAL EDIT === -->
<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formEdit" method="POST">
        @csrf @method('PUT')
        <div class="modal-header"><h5 class="modal-title">Edit Presensi</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <ul class="list-group list-group-flush mb-3">
            <li class="list-group-item"><b>Nama:</b> <span id="edit-nama"></span></li>
            <li class="list-group-item"><b>Tanggal:</b> <span id="edit-tanggal"></span></li>
            <li class="list-group-item"><b>Waktu Scan:</b> <span id="edit-waktu_scan"></span></li>
          </ul>
          <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-select" required>
              <option value="tepat_waktu">Tepat Waktu</option>
              <option value="terlambat">Terlambat</option>
              <option value="izin">Izin</option>
              <option value="sakit">Sakit</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="3" placeholder="Keterangan (opsional)"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- === MODAL DELETE === -->
<div class="modal fade" id="modalDelete" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formDelete" method="POST">
        @csrf @method('DELETE')
        <div class="modal-header"><h5 class="modal-title">Hapus Presensi</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <p>Yakin ingin menghapus presensi siswa <strong id="delete-nama"></strong>?</p>
          <p class="text-muted">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-show')) {
        const btn = e.target.closest('.btn-show');
        const id = btn.dataset.id;
        fetch(`{{ url('guru/presensi') }}/${id}`).then(res => res.json()).then(data => {
            document.getElementById('show-nama').textContent = data.siswa?.nama || '-';
            document.getElementById('show-nisn').textContent = data.siswa?.nisn || '-';
            document.getElementById('show-kelas').textContent = data.siswa?.kelas || '-';
            document.getElementById('show-tanggal').textContent = data.tanggal;
            document.getElementById('show-waktu_scan').textContent = data.waktu_scan;
            document.getElementById('show-status').textContent = data.status;
            document.getElementById('show-keterangan').textContent = data.keterangan || '-';
            new bootstrap.Modal(document.getElementById('modalShow')).show();
        });
    }
    if (e.target.closest('.btn-edit')) {
        const btn = e.target.closest('.btn-edit');
        const id = btn.dataset.id;
        fetch(`{{ url('guru/presensi') }}/${id}/edit`).then(res => res.json()).then(data => {
            document.getElementById('edit-nama').textContent = data.siswa?.nama || '-';
            document.getElementById('edit-tanggal').textContent = data.tanggal;
            document.getElementById('edit-waktu_scan').textContent = data.waktu_scan;
            document.getElementById('formEdit').action = `{{ url('guru/presensi') }}/${id}`;
            document.querySelector('select[name="status"]').value = data.status;
            document.querySelector('textarea[name="keterangan"]').value = data.keterangan || '';
            new bootstrap.Modal(document.getElementById('modalEdit')).show();
        });
    }
    if (e.target.closest('.btn-delete')) {
        const btn = e.target.closest('.btn-delete');
        const id = btn.dataset.id;
        const nama = btn.dataset.nama;
        document.getElementById('delete-nama').textContent = nama;
        document.getElementById('formDelete').action = `{{ url('guru/presensi') }}/${id}`;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    }
});
</script>
@endpush
