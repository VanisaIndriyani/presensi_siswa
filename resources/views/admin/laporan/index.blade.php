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
</style>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 page-title">Laporan Presensi Siswa</h1>
    <div>
        <a href="{{ route('admin.laporan.exportExcel', array_filter(['tanggal' => $tanggal, 'kelas' => request('kelas')])) }}" class="btn btn-success me-2">Download Excel</a>
        <a href="{{ route('admin.laporan.exportPdf', array_filter(['tanggal' => $tanggal, 'kelas' => request('kelas')])) }}" class="btn btn-danger">Download PDF</a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-auto">
                <label class="form-label">Filter Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="{{ $tanggal ?? '' }}">
            </div>
            <div class="col-auto">
                <label class="form-label">Filter Kelas</label>
                <select name="kelas" class="form-select">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k }}" {{ request('kelas') == $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label">Cari Nama/NISN</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nama atau NISN">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
                <a href="{{ route('admin.laporan.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="mb-3">Rekap Presensi</h5>
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Kelas</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                       
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
                            <td>
                                {{ $p->jam_pulang ? $p->jam_pulang : '-' }}
                            </td>
                            <td>
                                @if($p->status == 'tepat_waktu')
                                    <span class="badge bg-success">Tepat Waktu</span>
                                @elseif($p->status == 'terlambat')
                                    <span class="badge bg-danger">Terlambat</span>
                                @elseif($p->status == 'sakit')
                                    <span class="badge bg-warning text-dark">Sakit</span>
                                @elseif($p->status == 'izin')
                                    <span class="badge bg-info text-dark">Izin</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($p->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($p->status == 'tepat_waktu' && empty($p->keterangan))
                                    Tepat waktu masuk sekolah
                                @else
                                    {{ $p->keterangan }}
                                @endif
                            </td>
                          
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada data presensi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 