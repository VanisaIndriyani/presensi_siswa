@extends('layouts.admin')

@section('content')
<style>
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        border-radius: 16px;
        padding: 1.5rem 2rem;
        min-width: 220px;
        box-shadow: 0 4px 16px rgba(102,126,234,0.08);
        display: flex;
        align-items: center;
        gap: 1.2rem;
        margin-bottom: 1.5rem;
        transition: transform 0.15s;
    }
    .stat-card:hover { transform: translateY(-2px) scale(1.03); }
    .stat-icon {
        font-size: 2.2rem;
        background: rgba(255,255,255,0.18);
        border-radius: 50%;
        width: 54px;
        height: 54px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .stat-info {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .stat-label {
        font-size: 1.1rem;
        opacity: 0.85;
        font-weight: 500;
    }
    .stat-value {
        font-size: 2.1rem;
        font-weight: bold;
        margin-top: 2px;
    }
    .dashboard-cards {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
        margin-bottom: 2rem;
    }
    .filter-range {
        margin-bottom: 1.2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    @media (max-width: 900px) {
        .dashboard-cards { flex-direction: column; gap: 1.2rem; }
    }
    
    /* Warna teks yang konsisten */
    .page-title {
        color: #7C3AED !important; /* Ungu untuk judul */
        font-weight: 700;
    }
    .chart-title {
        color: #3B82F6 !important; /* Biru untuk judul grafik */
        font-weight: 600;
    }
    .form-label {
        color: #7C3AED !important; /* Ungu untuk label */
        font-weight: 500;
    }
    
    /* Style untuk kartu yang bisa diklik */
    .stat-card.clickable {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .stat-card.clickable:hover {
        transform: translateY(-4px) scale(1.05);
        box-shadow: 0 8px 25px rgba(102,126,234,0.15);
    }
</style>
<h1 class="page-title" style="font-size:2.2rem; margin-bottom:2rem;">Dashboard Admin</h1>
<div class="dashboard-cards">
    <div class="stat-card" style="background: linear-gradient(135deg, #38bdf8 0%, #6366f1 100%);">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <div class="stat-label">Total Siswa</div>
            <div class="stat-value">{{ $totalSiswa ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-card clickable" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);" onclick="showSiswaByStatus('tepat_waktu')">
        <div class="stat-icon"><i class="fas fa-user-check"></i></div>
        <div class="stat-info">
            <div class="stat-label">Hadir Hari Ini</div>
            <div class="stat-value">{{ $hadirHariIni ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-card clickable" style="background: linear-gradient(135deg, #ef4444 0%, #f59e42 100%);" onclick="showSiswaByStatus('terlambat')">
        <div class="stat-icon"><i class="fas fa-user-clock"></i></div>
        <div class="stat-info">
            <div class="stat-label">Terlambat Hari Ini</div>
            <div class="stat-value">{{ $terlambatHariIni ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-card clickable" style="background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%);" onclick="showSiswaByStatus('sakit')">
        <div class="stat-icon"><i class="fas fa-user-injured"></i></div>
        <div class="stat-info">
            <div class="stat-label">Sakit Hari Ini</div>
            <div class="stat-value">{{ $sakitHariIni ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-card clickable" style="background: linear-gradient(135deg, #f472b6 0%, #facc15 100%);" onclick="showSiswaByStatus('izin')">
        <div class="stat-icon"><i class="fas fa-user-shield"></i></div>
        <div class="stat-info">
            <div class="stat-label">Izin Hari Ini</div>
            <div class="stat-value">{{ $izinHariIni ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-card clickable" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);" onclick="showSiswaByStatus('absen')">
        <div class="stat-icon"><i class="fas fa-user-times"></i></div>
        <div class="stat-info">
            <div class="stat-label">Absen Hari Ini</div>
            <div class="stat-value">{{ ($totalSiswa ?? 0) - ($hadirHariIni ?? 0) - ($terlambatHariIni ?? 0) - ($sakitHariIni ?? 0) - ($izinHariIni ?? 0) - ($alpaHariIni ?? 0) }}</div>
        </div>
    </div>
    <div class="stat-card clickable" style="background: linear-gradient(135deg, #6b7280 0%, #a1a1aa 100%);" onclick="showSiswaByStatus('alpa')">
        <div class="stat-icon"><i class="fas fa-user-slash"></i></div>
        <div class="stat-info">
            <div class="stat-label">Alpa Hari Ini</div>
            <div class="stat-value">{{ $alpaHariIni ?? 0 }}</div>
        </div>
    </div>
</div>
@if(isset($liburHariIni) && $liburHariIni)
    <div class="alert alert-info mb-3">
        <b>Hari ini libur:</b> {{ $liburHariIni->keterangan }}
    </div>
@endif
<div class="card mt-4 mb-4" style="border-radius:14px;">
    <div class="card-body">
        <div class="filter-range">
            <form method="GET" class="d-flex align-items-center gap-2">
                <label for="range" class="me-2 mb-0"><i class="fas fa-filter me-1"></i>Filter Grafik:</label>
                <select name="range" id="range" class="form-select" style="width:auto; min-width:140px;" onchange="this.form.submit()">
                    <option value="7hari" {{ $range=='7hari' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="minggu" {{ $range=='minggu' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="bulan" {{ $range=='bulan' ? 'selected' : '' }}>Bulan Ini</option>
                </select>
            </form>
        </div>
        <h5 class="chart-title mb-3"><i class="fas fa-chart-bar me-2"></i>Grafik Kehadiran</h5>
        <canvas id="kehadiranChart" height="90"></canvas>
    </div>
</div>

<!-- Modal untuk menampilkan daftar siswa -->
<div class="modal fade" id="modalSiswa" tabindex="-1" aria-labelledby="modalSiswaTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSiswaTitle">Daftar Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Loading -->
                <div id="loadingSiswa" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data siswa...</p>
                </div>
                
                <!-- Tabel siswa -->
                <div id="siswaList" class="d-none">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>NISN</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="siswaTableBody">
                                <!-- Data akan diisi oleh JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pesan kosong -->
                <div id="emptySiswa" class="text-center py-4 d-none">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada data</h5>
                    <p class="text-muted">Tidak ada siswa dengan status ini hari ini.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('kehadiranChart').getContext('2d');
const kehadiranChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($labels),
        datasets: [
            {
                label: 'Tepat Waktu',
                data: @json($hadirData),
                backgroundColor: '#22c55e'
            },
            {
                label: 'Terlambat',
                data: @json($terlambatData),
                backgroundColor: '#facc15'
            },
            {
                label: 'Izin',
                data: @json($izinData),
                backgroundColor: '#38bdf8'
            },
            {
                label: 'Sakit',
                data: @json($sakitData),
                backgroundColor: '#818cf8'
            },
            {
                label: 'Absen',
                data: @json($absenData),
                backgroundColor: '#ef4444'
            },
            {
                label: 'Alpa',
                data: @json($alpaData),
                backgroundColor: '#6b7280'
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            title: { display: false }
        },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});

// Fungsi untuk menampilkan siswa berdasarkan status
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

// Fungsi untuk menentukan warna badge berdasarkan status
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
@endsection
