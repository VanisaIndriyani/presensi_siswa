@extends('layouts.guru')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Info Guru -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8 col-12 mb-3 mb-md-0">
                        <h5 class="card-title mb-1">
                            <i class="fas fa-chalkboard-teacher text-primary me-2"></i>
                            Selamat Datang
                        </h5>
                        <p class="text-muted mb-0">
                            Guru dapat melihat seluruh siswa di sekolah.
                        </p>
                    </div>
                    <div class="col-md-4 col-12">
                        <div class="d-flex justify-content-between justify-content-md-end">
                            <div class="text-center me-3">
                                <div class="h4 mb-0 text-primary">{{ $statistik['total_siswa'] }}</div>
                                <small class="text-muted">Total Siswa</small>
                            </div>
                            <div class="text-center">
                                <div class="h4 mb-0 text-success">{{ $statistik['hadir'] }}</div>
                                <small class="text-muted">Hadir Hari Ini</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik -->
    <div class="col-12 col-sm-6 col-lg-3 mb-4">
        <div class="card bg-success text-white text-center">
            <div class="card-body">
                <i class="fas fa-check-circle fa-2x mb-2"></i>
                <h3 class="mb-1">{{ $statistik['hadir'] }}</h3>
                <p class="mb-0">Tepat Waktu</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-4">
        <div class="card bg-warning text-white text-center">
            <div class="card-body">
                <i class="fas fa-clock fa-2x mb-2"></i>
                <h3 class="mb-1">{{ $statistik['terlambat'] }}</h3>
                <p class="mb-0">Terlambat</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-4">
        <div class="card bg-danger text-white text-center">
            <div class="card-body">
                <i class="fas fa-times-circle fa-2x mb-2"></i>
                <h3 class="mb-1">{{ $statistik['absen'] }}</h3>
                <p class="mb-0">Absen</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-4">
        <div class="card bg-info text-white text-center">
            <div class="card-body">
                <i class="fas fa-percentage fa-2x mb-2"></i>
                <h3 class="mb-1">
                    @if($statistik['total_siswa'] > 0)
                        {{ round((($statistik['hadir'] + $statistik['terlambat']) / $statistik['total_siswa']) * 100) }}%
                    @else
                        0%
                    @endif
                </h3>
                <p class="mb-0">Kehadiran</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Show -->
<div class="modal fade" id="modalShow" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Presensi</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
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

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEdit" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Presensi</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
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
                        <textarea name="keterangan" class="form-control" rows="3"></textarea>
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

<!-- Modal Delete -->
<div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formDelete" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Presensi</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Yakin ingin menghapus presensi siswa <strong id="delete-nama"></strong>?</p>
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
document.querySelectorAll('.btn-show').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        fetch(`/guru/presensi/${id}`)
            .then(res => res.json())
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

document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        fetch(`/guru/presensi/${id}/edit`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('edit-nama').textContent = data.siswa?.nama || '-';
                document.getElementById('edit-tanggal').textContent = data.tanggal;
                document.getElementById('edit-waktu_scan').textContent = data.waktu_scan;
                document.querySelector('select[name="status"]').value = data.status;
                document.querySelector('textarea[name="keterangan"]').value = data.keterangan || '';
                document.getElementById('formEdit').action = `/guru/presensi/${id}`;
                new bootstrap.Modal(document.getElementById('modalEdit')).show();
            });
    });
});

document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        const nama = btn.dataset.nama;
        document.getElementById('delete-nama').textContent = nama;
        document.getElementById('formDelete').action = `/guru/presensi/${id}`;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    });
});
</script>
@endpush
