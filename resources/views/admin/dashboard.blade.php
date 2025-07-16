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
</style>
<h1 style="font-size:2.2rem; font-weight:bold; margin-bottom:2rem; color:#22336b;">Dashboard Admin</h1>
<div class="dashboard-cards">
    <div class="stat-card" style="background: linear-gradient(135deg, #38bdf8 0%, #6366f1 100%);">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <div class="stat-label">Total Siswa</div>
            <div class="stat-value">{{ $totalSiswa ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);">
        <div class="stat-icon"><i class="fas fa-user-check"></i></div>
        <div class="stat-info">
            <div class="stat-label">Hadir Hari Ini</div>
            <div class="stat-value">{{ $hadirHariIni ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #ef4444 0%, #f59e42 100%);">
        <div class="stat-icon"><i class="fas fa-user-clock"></i></div>
        <div class="stat-info">
            <div class="stat-label">Terlambat Hari Ini</div>
            <div class="stat-value">{{ $terlambatHariIni ?? 0 }}</div>
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
        <h5 class="mb-3" style="color:#22336b;"><i class="fas fa-chart-bar me-2"></i>Grafik Kehadiran</h5>
        <canvas id="kehadiranChart" height="90"></canvas>
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
                label: 'Absen',
                data: @json($absenData),
                backgroundColor: '#ef4444'
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
</script>
@endpush
@endsection
