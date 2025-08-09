@extends('layouts.kepala-sekolah')

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
    
    /* Notification Bell Styles */
    .notification-bell {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(239, 68, 68, 0.3);
        transition: all 0.3s ease;
        animation: bellShake 2s infinite;
    }
    .notification-bell:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
    }
    .notification-bell .badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #fbbf24;
        color: #1f2937;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: bold;
    }
    @keyframes bellShake {
        0%, 100% { transform: rotate(0deg); }
        10%, 30%, 50%, 70%, 90% { transform: rotate(5deg); }
        20%, 40%, 60%, 80% { transform: rotate(-5deg); }
    }
    .notification-bell.hidden {
        display: none;
    }
</style>
<h1 class="page-title" style="font-size:2.2rem; margin-bottom:2rem;">Dashboard Kepala Sekolah</h1>

@if(isset($alpaHariIni) && $alpaHariIni > 0)
    @php
        $totalSiswaHariIni = ($hadirHariIni ?? 0) + ($terlambatHariIni ?? 0) + ($sakitHariIni ?? 0) + ($izinHariIni ?? 0) + ($alpaHariIni ?? 0);
        $persentaseAlpa = $totalSiswaHariIni > 0 ? round(($alpaHariIni / $totalSiswaHariIni) * 100, 1) : 0;
    @endphp
    
    @if($alpaHariIni >= 3)
        <div class="notification-bell" onclick="showAlpaModal()" title="Klik untuk melihat detail siswa yang alpa">
            <i class="fas fa-bell"></i>
            <div class="badge">{{ $alpaHariIni }}</div>
        </div>
    @endif
@endif
<div class="dashboard-cards">
    <div class="stat-card" style="background: linear-gradient(135deg, #38bdf8 0%, #6366f1 100%);" onclick="showSiswaByStatus('total')" style="cursor: pointer;">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <div class="stat-label">Total Siswa</div>
            <div class="stat-value">{{ $totalSiswa ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);" onclick="showSiswaByStatus('tepat_waktu')" style="cursor: pointer;">
        <div class="stat-icon"><i class="fas fa-user-check"></i></div>
        <div class="stat-info">
            <div class="stat-label">Hadir Hari Ini</div>
            <div class="stat-value">{{ $hadirHariIni ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #ef4444 0%, #f59e42 100%);" onclick="showSiswaByStatus('terlambat')" style="cursor: pointer;">
        <div class="stat-icon"><i class="fas fa-user-clock"></i></div>
        <div class="stat-info">
            <div class="stat-label">Terlambat Hari Ini</div>
            <div class="stat-value">{{ $terlambatHariIni ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%);" onclick="showSiswaByStatus('sakit')" style="cursor: pointer;">
        <div class="stat-icon"><i class="fas fa-user-injured"></i></div>
        <div class="stat-info">
            <div class="stat-label">Sakit Hari Ini</div>
            <div class="stat-value">{{ $sakitHariIni ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #f472b6 0%, #facc15 100%);" onclick="showSiswaByStatus('izin')" style="cursor: pointer;">
        <div class="stat-icon"><i class="fas fa-user-shield"></i></div>
        <div class="stat-info">
            <div class="stat-label">Izin Hari Ini</div>
            <div class="stat-value">{{ $izinHariIni ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #6b7280 0%, #a1a1aa 100%);" onclick="showSiswaByStatus('alpa')" style="cursor: pointer;">
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

@if(isset($alpaHariIni))
    @if($alpaHariIni > 0)
    @php
        $totalSiswaHariIni = ($hadirHariIni ?? 0) + ($terlambatHariIni ?? 0) + ($sakitHariIni ?? 0) + ($izinHariIni ?? 0) + ($alpaHariIni ?? 0);
        $persentaseAlpa = $totalSiswaHariIni > 0 ? round(($alpaHariIni / $totalSiswaHariIni) * 100, 1) : 0;
    @endphp
    
    @if($alpaHariIni >= 5 || $persentaseAlpa >= 10)
        <div class="alert alert-danger mb-3" style="border-radius: 12px; border-left: 4px solid #dc3545; position: relative;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <h6 class="alert-heading mb-1">
                            Peringatan Tingkat Ketidakhadiran Tinggi!
                        </h6>
                        <p class="mb-1">
                            <strong>{{ $alpaHariIni }} siswa</strong> tidak hadir hari ini 
                            @if($totalSiswaHariIni > 0)
                                ({{ $persentaseAlpa }}% dari total kehadiran hari ini)
                            @endif
                        </p>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Perlu tindakan segera untuk mengecek dan menindaklanjuti ketidakhadiran siswa.
                        </small>
                    </div>
                </div>
                <div class="notification-bell" style="position: absolute; top: 15px; right: 15px;">
                    <i class="fas fa-bell" style="font-size: 1.2rem; color: #dc3545;"></i>
                </div>
            </div>
        </div>
    @elseif($alpaHariIni >= 3)
        <div class="alert alert-warning mb-3" style="border-radius: 12px; border-left: 4px solid #ffc107; position: relative;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <h6 class="alert-heading mb-1">
                            Perhatian: Ketidakhadiran Siswa
                        </h6>
                        <p class="mb-1">
                            <strong>{{ $alpaHariIni }} siswa</strong> tidak hadir hari ini
                            @if($totalSiswaHariIni > 0)
                                ({{ $persentaseAlpa }}% dari total kehadiran hari ini)
                            @endif
                        </p>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Disarankan untuk memantau dan mengecek alasan ketidakhadiran.
                        </small>
                    </div>
                </div>
                <div class="notification-bell" style="position: absolute; top: 15px; right: 15px;">
                    <i class="fas fa-bell" style="font-size: 1.2rem; color: #ffc107;"></i>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-success mb-3" style="border-radius: 12px; border-left: 4px solid #198754; position: relative;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <h6 class="alert-heading mb-1">
                            Kehadiran Siswa Baik
                        </h6>
                        <p class="mb-1">
                            Hanya <strong>{{ $alpaHariIni }} siswa</strong> yang tidak hadir hari ini
                            @if($totalSiswaHariIni > 0)
                                ({{ $persentaseAlpa }}% dari total kehadiran hari ini)
                            @endif
                        </p>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Tingkat kehadiran siswa dalam kondisi baik.
                        </small>
                    </div>
                </div>
                <div class="notification-bell" style="position: absolute; top: 15px; right: 15px;">
                    <i class="fas fa-bell" style="font-size: 1.2rem; color: #198754;"></i>
                </div>
            </div>
        </div>
     @endif
     @else
         <div class="alert alert-success mb-3" style="border-radius: 12px; border-left: 4px solid #198754;">
             <div class="d-flex align-items-center">
                 <i class="fas fa-star me-3" style="font-size: 1.5rem;"></i>
                 <div>
                     <h6 class="alert-heading mb-1">
                         <i class="fas fa-trophy me-2"></i>Kehadiran Sempurna!
                     </h6>
                     <p class="mb-1">
                         <strong>Tidak ada siswa yang alpa</strong> hari ini
                     </p>
                     <small class="text-muted">
                         <i class="fas fa-info-circle me-1"></i>
                         Semua siswa hadir atau memberikan keterangan yang valid.
                     </small>
                 </div>
             </div>
         </div>
     @endif
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

<!-- Modal Detail Siswa Alpa -->
<div class="modal fade" id="modalSiswaAlpa" tabindex="-1" aria-labelledby="modalSiswaAlpaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalSiswaAlpaLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Detail Siswa yang Alpa Hari Ini
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="loadingAlpa" class="text-center">
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data siswa...</p>
                </div>
                <div id="siswaAlpaList" class="d-none">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-danger">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>NISN</th>
                                    <th>Kelas</th>
                                    <th>Jenis Kelamin</th>
                                </tr>
                            </thead>
                            <tbody id="siswaAlpaTableBody"></tbody>
                        </table>
                    </div>
                </div>
                <div id="emptyAlpa" class="text-center d-none">
                    <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Tidak ada siswa yang alpa</h5>
                    <p class="text-muted">Semua siswa hadir atau memberikan keterangan yang valid.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="{{ route('kepala-sekolah.laporan.index') }}" class="btn btn-primary">
                    <i class="fas fa-chart-bar me-2"></i>Lihat Laporan Lengkap
                </a>
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
    animation: false,
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
        animation: false,
        plugins: {
            legend: { position: 'top' },
            title: { display: false }
        },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});

// Function untuk menampilkan modal siswa alpa
function showAlpaModal() {
    const modal = new bootstrap.Modal(document.getElementById('modalSiswaAlpa'));
    const loading = document.getElementById('loadingAlpa');
    const siswaList = document.getElementById('siswaAlpaList');
    const emptyAlpa = document.getElementById('emptyAlpa');
    const tableBody = document.getElementById('siswaAlpaTableBody');
    
    // Reset modal state
    loading.classList.remove('d-none');
    siswaList.classList.add('d-none');
    emptyAlpa.classList.add('d-none');
    tableBody.innerHTML = '';
    
    modal.show();
    
    // Fetch data siswa yang alpa
    fetch('/kepala-sekolah/siswa-alpa')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            loading.classList.add('d-none');
            
            // Check if data has error property
            if (data.error) {
                throw new Error(data.error);
            }
            
            if (data.length > 0) {
                siswaList.classList.remove('d-none');
                data.forEach((siswa, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${siswa.nama || '-'}</td>
                        <td>${siswa.nisn || '-'}</td>
                        <td>${siswa.kelas || '-'}</td>
                        <td>${siswa.jenis_kelamin || '-'}</td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                emptyAlpa.classList.remove('d-none');
            }
        })
        .catch(error => {
            console.error('Error fetching siswa alpa:', error);
            loading.classList.add('d-none');
            alert('Gagal memuat data siswa yang alpa: ' + error.message);
        });
}

// Function untuk menampilkan detail siswa berdasarkan status
function showSiswaByStatus(status) {
    const modal = new bootstrap.Modal(document.getElementById('modalSiswaAlpa'));
    const loading = document.getElementById('loadingAlpa');
    const siswaList = document.getElementById('siswaAlpaList');
    const emptyAlpa = document.getElementById('emptyAlpa');
    const tableBody = document.getElementById('siswaAlpaTableBody');

    // Reset modal state
    loading.classList.remove('d-none');
    siswaList.classList.add('d-none');
    emptyAlpa.classList.add('d-none');
    tableBody.innerHTML = '';

    modal.show();

    // Fetch data siswa berdasarkan status
    fetch(`/kepala-sekolah/siswa-by-status/${status}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            loading.classList.add('d-none');
            
            // Check if data has error property
            if (data.error) {
                throw new Error(data.error);
            }
            
            if (data.length > 0) {
                siswaList.classList.remove('d-none');
                data.forEach((siswa, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${siswa.nama || '-'}</td>
                        <td>${siswa.nisn || '-'}</td>
                        <td>${siswa.kelas || '-'}</td>
                        <td>${siswa.jenis_kelamin || '-'}</td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                emptyAlpa.classList.remove('d-none');
            }
        })
        .catch(error => {
            console.error('Error fetching siswa by status:', error);
            loading.classList.add('d-none');
            alert('Gagal memuat data siswa berdasarkan status: ' + error.message);
        });
}

// Auto-hide bell notification after 10 seconds
setTimeout(() => {
    const bell = document.querySelector('.notification-bell');
    if (bell) {
        bell.style.animation = 'none';
        bell.style.opacity = '0.7';
    }
}, 10000);
</script>
@endpush
@endsection
