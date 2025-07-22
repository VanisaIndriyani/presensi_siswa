@extends('layouts.kepala-sekolah')

@section('title', 'Laporan Presensi')

<style>
    .page-header {
        background: linear-gradient(135deg, #9966CC 0%, #663399 100%);
        color: white;
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(153, 102, 204, 0.3);
    }
    
    .page-title {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .page-subtitle {
        opacity: 0.9;
        font-size: 1rem;
    }
    
    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
    }
    
    .action-btn {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }
    
    .action-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    }
    
    .filter-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: none;
        overflow: hidden;
    }
    
    .filter-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1.5rem;
        border-bottom: 1px solid #dee2e6;
    }
    
    .filter-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #495057;
        margin: 0;
    }
    
    .form-control, .form-select {
        border-radius: 12px;
        border: 2px solid #e9ecef;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #9966CC;
        box-shadow: 0 0 0 0.2rem rgba(153, 102, 204, 0.15);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #9966CC 0%, #663399 100%);
        border: none;
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(153, 102, 204, 0.4);
    }
    
    .btn-secondary {
        background: #6c757d;
        border: none;
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }
    
    .table-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: none;
        overflow: hidden;
    }
    
    .table-header {
        background: linear-gradient(135deg, #9966CC 0%, #663399 100%);
        color: white;
        padding: 1.5rem;
        border-bottom: none;
    }
    
    .table-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin: 0;
    }
    
    .table thead th {
        background: linear-gradient(135deg, #9966CC 0%, #663399 100%);
        color: white;
        font-weight: 600;
        border: none;
        padding: 1rem 0.75rem;
        font-size: 0.9rem;
    }
    
    .table tbody td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid #f8f9fa;
    }
    
    .table-striped > tbody > tr:nth-of-type(odd) > td {
        background-color: rgba(153, 102, 204, 0.02);
    }
    
    .table tbody tr:hover {
        background-color: rgba(153, 102, 204, 0.05);
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.5em 0.75em;
        border-radius: 8px;
        font-weight: 600;
    }
    
    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }
    
    .summary-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: none;
        transition: all 0.3s ease;
    }
    
    .summary-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    }
    
    .summary-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .summary-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .summary-label {
        color: #6c757d;
        font-weight: 500;
    }
    
    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    .pagination {
        margin-top: 2rem;
    }
    
    .page-link {
        border-radius: 8px;
        margin: 0 2px;
        border: 1px solid #dee2e6;
        color: #9966CC;
        font-weight: 500;
    }
    
    .page-link:hover {
        background-color: #9966CC;
        border-color: #9966CC;
        color: white;
    }
    
    .page-item.active .page-link {
        background: linear-gradient(135deg, #9966CC 0%, #663399 100%);
        border-color: #9966CC;
    }
    
    @media (max-width: 768px) {
        .page-header {
            padding: 1.5rem;
        }
        
        .page-title {
            font-size: 1.8rem;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .summary-cards {
            grid-template-columns: 1fr;
        }
    }
</style>

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title">Laporan Presensi</h1>
            <p class="page-subtitle">Kelola dan analisis data kehadiran siswa</p>
        </div>
        <div class="action-buttons">
            <a href="{{ route('kepala-sekolah.laporan.exportExcel') }}" class="action-btn">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </a>
            <a href="{{ route('kepala-sekolah.laporan.exportPdf') }}" class="action-btn">
                <i class="fas fa-file-pdf me-2"></i>Export PDF
            </a>
        </div>
    </div>
</div>
<!-- Summary Cards -->
@if($presensis->count() > 0)
    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-icon" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="summary-value text-success">{{ $presensis->where('status', 'tepat_waktu')->count() }}</div>
            <div class="summary-label">Tepat Waktu</div>
        </div>
        
        <div class="summary-card">
            <div class="summary-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="summary-value text-warning">{{ $presensis->where('status', 'terlambat')->count() }}</div>
            <div class="summary-label">Terlambat</div>
        </div>
        
        <div class="summary-card">
            <div class="summary-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white;">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="summary-value text-danger">{{ $presensis->where('status', 'alpa')->count() }}</div>
            <div class="summary-label">Alpa</div>
        </div>
        
        <div class="summary-card">
            <div class="summary-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
                <i class="fas fa-user-clock"></i>
            </div>
            <div class="summary-value text-info">{{ $presensis->whereIn('status', ['izin', 'sakit'])->count() }}</div>
            <div class="summary-label">Izin/Sakit</div>
        </div>
    </div>
@endif

<!-- Filter -->
<div class="filter-card mb-4">
    <div class="filter-header">
        <h5 class="filter-title">
            <i class="fas fa-filter me-2"></i>
            Filter Laporan
        </h5>
    </div>
    <div class="card-body p-4">
        <form method="GET" action="{{ route('kepala-sekolah.laporan.index') }}">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Tanggal Awal</label>
                    <input type="date" name="tanggal_awal" class="form-control" value="{{ request('tanggal_awal') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-semibold">Kelas</label>
                    <select name="kelas" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas }}" {{ request('kelas') == $kelas ? 'selected' : '' }}>
                                {{ $kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach($statusList as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Filter
                        </button>
                        <a href="{{ route('kepala-sekolah.laporan.index') }}" class="btn btn-secondary">
                            <i class="fas fa-refresh me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @if($presensis->count() > 0)
        <div class="card-footer bg-light">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Menampilkan {{ $presensis->firstItem() }} sampai {{ $presensis->lastItem() }} dari {{ $presensis->total() }} data
            </small>
        </div>
    @endif
</div>

<!-- Tabel Laporan -->
<div class="table-card">
    <div class="table-header d-flex justify-content-between align-items-center">
        <h5 class="table-title">
            <i class="fas fa-table me-2"></i>
            Data Presensi
        </h5>
        <span class="badge bg-light text-dark fs-6">
            Total: {{ $presensis->total() }} data
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 60px;">No</th>
                        <th style="width: 100px;">Tanggal</th>
                        <th>Nama Siswa</th>
                        <th style="width: 80px;">Kelas</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 80px;">Jam Masuk</th>
                        <th style="width: 80px;">Jam Pulang</th>
                        <th>Guru</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($presensis as $i => $presensi)
                        <tr>
                            <td class="text-center fw-semibold">{{ $presensis->firstItem() + $i }}</td>
                            <td class="fw-semibold">{{ \Carbon\Carbon::parse($presensi->tanggal)->format('d/m/Y') }}</td>
                            <td class="fw-semibold">{{ $presensi->nama_siswa }}</td>
                            <td class="text-center fw-semibold">{{ $presensi->kelas }}</td>
                            <td>
                                @if($presensi->status == 'tepat_waktu')
                                    <span class="badge bg-success">Tepat Waktu</span>
                                @elseif($presensi->status == 'terlambat')
                                    <span class="badge bg-warning">Terlambat</span>
                                @elseif($presensi->status == 'izin')
                                    <span class="badge bg-info">Izin</span>
                                @elseif($presensi->status == 'sakit')
                                    <span class="badge bg-secondary">Sakit</span>
                                @elseif($presensi->status == 'alpa')
                                    <span class="badge bg-danger">Alpa</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($presensi->status) }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($presensi->waktu_scan)
                                    {{ \Carbon\Carbon::parse($presensi->waktu_scan)->format('H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                @if($presensi->jam_pulang)
                                    {{ \Carbon\Carbon::parse($presensi->jam_pulang)->format('H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $presensi->guru->nama ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h5 class="mb-2">Tidak ada data presensi</h5>
                                <p class="mb-0">Belum ada data presensi yang ditemukan untuk periode yang dipilih.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
@if($presensis->hasPages())
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation">
            {{ $presensis->appends(request()->query())->links('pagination::bootstrap-5') }}
        </nav>
    </div>
@endif


@endsection 