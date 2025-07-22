@extends('layouts.guru')

@section('title', 'Dashboard')

<style>
    .rounded-circle {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .card.border-primary {
        border-width: 2px !important;
    }
    .card-header.bg-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
    .text-info {
        color: #17a2b8 !important;
    }
    .bg-primary.rounded-circle {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
    .bg-success.rounded-circle {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
    }
    .bg-info.rounded-circle {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
    .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .card[onclick] {
        cursor: pointer;
    }
    .card[onclick]:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    .current-time, .current-date {
        font-weight: 600;
        color: #667eea;
    }
    .fas.fa-circle.text-success {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
</style>

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
                            Selamat Datang, {{ $guru ? $guru->nama : 'Guru' }}!
                        </h5>
                        <p class="text-muted mb-0">
                            @if($guru)
                                Selamat datang di Sistem Presensi Siswa. Anda dapat melihat seluruh siswa di sekolah.

                                <br><small class="text-muted"><i class="fas fa-calendar me-1"></i><span class="current-date">{{ now()->format('l, d F Y') }}</span></small>
                                <br><small class="text-muted"><i class="fas fa-clock me-1"></i><span class="current-time">{{ now()->format('H:i:s') }}</span></small>
                                @if($jamMasuk)
                                    <br><small class="text-success"><i class="fas fa-school me-1"></i>Jam Masuk: {{ $jamMasuk->start_time }} - {{ $jamMasuk->end_time }}</small>
                                @endif
                            @else
                                Guru dapat melihat seluruh siswa di sekolah.
                                <br><small class="text-muted"><i class="fas fa-calendar me-1"></i><span class="current-date">{{ now()->format('l, d F Y') }}</span></small>
                                <br><small class="text-muted"><i class="fas fa-clock me-1"></i><span class="current-time">{{ now()->format('H:i:s') }}</span></small>
                                @if($jamMasuk)
                                    <br><small class="text-success"><i class="fas fa-school me-1"></i>Jam Masuk: {{ $jamMasuk->start_time }} - {{ $jamMasuk->end_time }}</small>
                                @endif
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4 col-12">
                        <div class="d-flex justify-content-between justify-content-md-end">
                            <div class="text-center me-3">
                                <div class="h4 mb-0 text-primary">{{ $statistik['total_siswa'] }}</div>
                                <small class="text-muted">Total Siswa</small>
                            </div>
                            <div class="text-center me-3">
                                <div class="h4 mb-0 text-success">{{ $statistik['hadir'] }}</div>
                                <small class="text-muted">Hadir Hari Ini</small>
                            </div>
                            <div class="text-center">
                                <div class="h4 mb-0 text-info">
                                    <i class="fas fa-circle text-success"></i>
                                </div>
                                <small class="text-muted">Online</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Guru Detail -->
    @if($guru)
    <div class="col-12 mb-4">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-user-tie me-2"></i>Informasi Guru</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary rounded-circle p-2 me-3">
                                <i class="fas fa-id-card text-white"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">NIP</small>
                                <strong>{{ $guru->nip }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-success rounded-circle p-2 me-3">
                                <i class="fas fa-envelope text-white"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Email</small>
                                <strong>{{ $guru->email }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-info rounded-circle p-2 me-3">
                                <i class="fas fa-venus-mars text-white"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Jenis Kelamin</small>
                                <strong>{{ $guru->jenis_kelamin ?: 'Belum diisi' }}</strong>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistik -->
    <div class="col-12 col-sm-6 col-lg-3 mb-4">
        <div class="card bg-success text-white text-center" style="cursor: pointer;" onclick="showSiswaByStatus('tepat_waktu')">
            <div class="card-body">
                <i class="fas fa-check-circle fa-2x mb-2"></i>
                <h3 class="mb-1">{{ $statistik['hadir'] }}</h3>
                <p class="mb-0">Tepat Waktu</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-4">
        <div class="card bg-warning text-white text-center" style="cursor: pointer;" onclick="showSiswaByStatus('terlambat')">
            <div class="card-body">
                <i class="fas fa-clock fa-2x mb-2"></i>
                <h3 class="mb-1">{{ $statistik['terlambat'] }}</h3>
                <p class="mb-0">Terlambat</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-4">
        <div class="card bg-danger text-white text-center" style="cursor: pointer;" onclick="showSiswaByStatus('absen')">
            <div class="card-body">
                <i class="fas fa-times-circle fa-2x mb-2"></i>
                <h3 class="mb-1">{{ $statistik['absen'] }}</h3>
                <p class="mb-0">Absen</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-4">
        <div class="card bg-secondary text-white text-center" style="cursor: pointer;" onclick="showSiswaByStatus('sakit')">
            <div class="card-body">
                <i class="fas fa-user-injured fa-2x mb-2"></i>
                <h3 class="mb-1">{{ $statistik['sakit'] ?? 0 }}</h3>
                <p class="mb-0">Sakit</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-4">
        <div class="card bg-info text-white text-center" style="cursor: pointer;" onclick="showSiswaByStatus('izin')">
            <div class="card-body">
                <i class="fas fa-user-check fa-2x mb-2"></i>
                <h3 class="mb-1">{{ $statistik['izin'] ?? 0 }}</h3>
                <p class="mb-0">Izin</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-4">
        <div class="card bg-secondary text-white text-center" style="cursor: pointer;" onclick="showSiswaByStatus('alpa')">
            <div class="card-body">
                <i class="fas fa-times-circle fa-2x mb-2"></i>
                <h3 class="mb-1">{{ $statistik['alpa'] ?? 0 }}</h3>
                <p class="mb-0">Alpa</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-4">
        <div class="card bg-info text-white text-center" style="cursor: pointer;" onclick="showSiswaByStatus('kehadiran')">
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
                <small class="opacity-75">{{ $statistik['hadir'] + $statistik['terlambat'] }}/{{ $statistik['total_siswa'] }} siswa</small>
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
                        <li class="list-group-item"><b>Jam Masuk:</b> <span id="edit-waktu_scan"></span></li>
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

<!-- Modal Daftar Siswa -->
<div class="modal fade" id="modalSiswa" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSiswaTitle">Daftar Siswa</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="loadingSiswa" class="text-center d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data siswa...</p>
                </div>
                <div id="siswaList" class="d-none">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>NISN</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="siswaTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="emptySiswa" class="text-center d-none">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Tidak ada data siswa untuk status ini</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Update waktu real-time
function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    const dateString = now.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    // Update elemen waktu jika ada
    const timeElements = document.querySelectorAll('.current-time');
    timeElements.forEach(el => {
        el.textContent = timeString;
    });
    
    const dateElements = document.querySelectorAll('.current-date');
    dateElements.forEach(el => {
        el.textContent = dateString;
    });
}

// Update waktu setiap detik
setInterval(updateTime, 1000);
updateTime(); // Update sekali saat halaman dimuat

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
        fetch(`{{ url('/guru/presensi') }}/${id}/edit`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('edit-nama').textContent = data.siswa?.nama || '-';
                document.getElementById('edit-tanggal').textContent = data.tanggal;
                document.getElementById('edit-waktu_scan').textContent = data.waktu_scan;
                document.querySelector('select[name="status"]').value = data.status;
                document.querySelector('textarea[name="keterangan"]').value = data.keterangan || '';
                document.getElementById('formEdit').action = `{{ url('/guru/presensi') }}/${id}`;
                new bootstrap.Modal(document.getElementById('modalEdit')).show();
            });
    });
});

document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        const nama = btn.dataset.nama;
        document.getElementById('delete-nama').textContent = nama;
        document.getElementById('formDelete').action = `{{ url('/guru/presensi') }}/${id}`;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    });
});

function showSiswaByStatus(status) {
    const modalTitle = document.getElementById('modalSiswaTitle');
    const loading = document.getElementById('loadingSiswa');
    const siswaList = document.getElementById('siswaList');
    const emptySiswa = document.getElementById('emptySiswa');
    const siswaTableBody = document.getElementById('siswaTableBody');

    // Mapping status untuk judul modal
    const statusLabels = {
        'tepat_waktu': 'Tepat Waktu',
        'terlambat': 'Terlambat',
        'absen': 'Absen',
        'sakit': 'Sakit',
        'izin': 'Izin',
        'alpa': 'Alpa',
        'kehadiran': 'Kehadiran'
    };

    modalTitle.textContent = `Daftar Siswa (Status: ${statusLabels[status] || status})`;
    loading.classList.remove('d-none');
    siswaList.classList.add('d-none');
    emptySiswa.classList.add('d-none');
    siswaTableBody.innerHTML = ''; // Clear previous data

    fetch(`{{ url('/api/siswa-by-status') }}/${status}`)
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(data => {
            loading.classList.add('d-none');
            
            // Check if data has error property
            if (data.error) {
                throw new Error(data.error);
            }
            
            if (data.length === 0) {
                siswaList.classList.add('d-none');
                emptySiswa.classList.remove('d-none');
            } else {
                siswaList.classList.remove('d-none');
                emptySiswa.classList.add('d-none');
                data.forEach((siswa, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${siswa.nama}</td>
                        <td>${siswa.nisn}</td>
                        <td>${siswa.kelas}</td>
                        <td><span class="badge bg-${getStatusBadgeColor(siswa.status)}">${siswa.status}</span></td>
                    `;
                    siswaTableBody.appendChild(row);
                });
            }
            new bootstrap.Modal(document.getElementById('modalSiswa')).show();
        })
        .catch(error => {
            console.error('Error fetching siswa by status:', error);
            loading.classList.add('d-none');
            alert('Gagal memuat data siswa: ' + error.message);
        });
}

function getStatusBadgeColor(status) {
    switch(status) {
        case 'Tepat Waktu': return 'success';
        case 'Terlambat': return 'warning';
        case 'Tidak Hadir': return 'danger';
        case 'Sakit': return 'secondary';
        case 'Izin': return 'info';
        case 'Alpa': return 'secondary';
        default: return 'primary';
    }
}
</script>
@endpush
