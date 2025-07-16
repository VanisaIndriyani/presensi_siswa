@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Laporan Presensi Siswa</h1>
    <div>
        <a href="{{ route('admin.laporan.exportExcel', ['tanggal' => $tanggal]) }}" class="btn btn-success me-2">Download Excel</a>
        <a href="{{ route('admin.laporan.exportPdf', ['tanggal' => $tanggal]) }}" class="btn btn-danger">Download PDF</a>
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
                        <th>Waktu Scan</th>
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
                                @if($p->status == 'tepat_waktu')
                                    <span class="badge bg-success">Tepat Waktu</span>
                                @else
                                    <span class="badge bg-danger">Terlambat</span>
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