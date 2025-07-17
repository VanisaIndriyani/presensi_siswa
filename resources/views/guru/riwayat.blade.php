@extends('layouts.guru')

@section('title', 'Riwayat Presensi')

@section('content')
<div class="row">
    <!-- Filter -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Siswa (Opsional)</label>
                        <select name="siswa_id" class="form-select">
                            <option value="">Semua Siswa</option>
                            @foreach($siswas as $siswa)
                                <option value="{{ $siswa->id }}" {{ $siswaId == $siswa->id ? 'selected' : '' }}>
                                    {{ $siswa->nama }} ({{ $siswa->nisn }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Awal</label>
                        <input type="date" name="tanggal_awal" value="{{ $tanggalAwal }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" value="{{ $tanggalAkhir }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-2"></i>Filter
                        </button>
                        <a href="{{ route('guru.riwayat') }}" class="btn btn-secondary">
                            <i class="fas fa-refresh me-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Statistik Riwayat -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-check fa-2x mb-2"></i>
                        <h4 class="mb-1">{{ $riwayatPresensi->count() }}</h4>
                        <p class="mb-0">Total Presensi</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h4 class="mb-1">{{ $riwayatPresensi->where('status', 'tepat_waktu')->count() }}</h4>
                        <p class="mb-0">Tepat Waktu</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <h4 class="mb-1">{{ $riwayatPresensi->where('status', 'terlambat')->count() }}</h4>
                        <p class="mb-0">Terlambat</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-percentage fa-2x mb-2"></i>
                        <h4 class="mb-1">
                            @if($riwayatPresensi->count() > 0)
                                {{ round(($riwayatPresensi->where('status', 'tepat_waktu')->count() / $riwayatPresensi->count()) * 100) }}%
                            @else
                                0%
                            @endif
                        </h4>
                        <p class="mb-0">Rata-rata Tepat Waktu</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Riwayat -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>
                    Riwayat Presensi
                    @if($siswaId)
                        - {{ $siswas->find($siswaId)->nama ?? 'Siswa' }}
                    @endif
                    ({{ \Carbon\Carbon::parse($tanggalAwal)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d/m/Y') }})
                </h5>
            </div>
            <div class="card-body">
                @if($guru && $guru->kelas)
                    @if($riwayatPresensi->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Nama Siswa</th>
                                        <th>NISN</th>
                                        <th>Jam Masuk</th>
                                        <th>Jam Pulang</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($riwayatPresensi as $index => $presensi)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ \Carbon\Carbon::parse($presensi->tanggal)->format('d/m/Y') }}</strong>
                                                <br>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($presensi->tanggal)->format('l') }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $presensi->siswa->nama }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $presensi->siswa->kelas }}</small>
                                            </td>
                                            <td>{{ $presensi->siswa->nisn }}</td>
                                            <td>
                                                <span class="text-success">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ \Carbon\Carbon::parse($presensi->waktu_scan)->format('H:i:s') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($presensi->jam_pulang)
                                                    <span class="text-primary">
                                                        <i class="fas fa-clock me-1"></i>
                                                        {{ \Carbon\Carbon::parse($presensi->jam_pulang)->format('H:i:s') }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($presensi->status === 'tepat_waktu')
                                                    <span class="status-badge status-tepat-waktu">
                                                        <i class="fas fa-check me-1"></i>Tepat Waktu
                                                    </span>
                                                @elseif($presensi->status === 'terlambat')
                                                    <span class="status-badge status-terlambat">
                                                        <i class="fas fa-clock me-1"></i>Terlambat
                                                    </span>
                                                @elseif($presensi->status === 'izin')
                                                    <span class="status-badge bg-info text-white">
                                                        <i class="fas fa-info-circle me-1"></i>Izin
                                                    </span>
                                                @elseif($presensi->status === 'sakit')
                                                    <span class="status-badge bg-warning text-dark">
                                                        <i class="fas fa-medkit me-1"></i>Sakit
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($presensi->keterangan)
                                                    <small class="text-muted">{{ $presensi->keterangan }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <!-- Hapus kolom aksi -->
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada riwayat presensi</h5>
                            <p class="text-muted">Tidak ada data presensi untuk periode yang dipilih</p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h5 class="text-warning">Kelas belum diatur</h5>
                        <p class="text-muted">Silakan hubungi admin untuk mengatur kelas Anda</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Show Presensi -->
<div class="modal fade" id="modalShow" tabindex="-1" aria-labelledby="modalShowLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalShowLabel">Detail Presensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><b>Nama Siswa:</b> <span id="show-nama"></span></li>
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

<!-- Modal Edit Presensi -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditLabel">Edit Presensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEdit" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item"><b>Nama Siswa:</b> <span id="edit-nama"></span></li>
                        <li class="list-group-item"><b>Tanggal:</b> <span id="edit-tanggal"></span></li>
                        <li class="list-group-item"><b>Waktu Scan:</b> <span id="edit-waktu_scan"></span></li>
                    </ul>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="tepat_waktu">Tepat Waktu</option>
                            <option value="terlambat">Terlambat</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Keterangan (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Show detail presensi
const showBtns = document.querySelectorAll('.btn-show');
showBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        fetch(`/guru/presensi/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('show-nama').textContent = data.siswa?.nama || '-';
                document.getElementById('show-nisn').textContent = data.siswa?.nisn || '-';
                document.getElementById('show-kelas').textContent = data.siswa?.kelas || '-';
                document.getElementById('show-tanggal').textContent = data.tanggal;
                document.getElementById('show-waktu_scan').textContent = data.waktu_scan;
                document.getElementById('show-status').textContent = data.status === 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat';
                document.getElementById('show-keterangan').textContent = data.keterangan || '-';
                new bootstrap.Modal(document.getElementById('modalShow')).show();
            });
    });
});

// Edit presensi
const editBtns = document.querySelectorAll('.btn-edit');
editBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        fetch(`/guru/presensi/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit-nama').textContent = data.siswa?.nama || '-';
                document.getElementById('edit-tanggal').textContent = data.tanggal;
                document.getElementById('edit-waktu_scan').textContent = data.waktu_scan;
                document.getElementById('formEdit').action = `/guru/presensi/${id}`;
                document.querySelector('select[name="status"]').value = data.status;
                document.querySelector('textarea[name="keterangan"]').value = data.keterangan || '';
                new bootstrap.Modal(document.getElementById('modalEdit')).show();
            });
    });
});
</script>
@endpush 