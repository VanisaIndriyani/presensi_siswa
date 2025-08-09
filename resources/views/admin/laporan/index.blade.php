@extends('layouts.admin')

@section('content')
<style>
    /* Warna teks yang lebih lembut */
    .page-title {
        color: #7C3AED !important; /* Ungu untuk judul */
        font-weight: 700;
    }
    .table thead th {
        background: #f8f9fa !important; /* Light gray background */
        color: #495057 !important; /* Dark gray text */
        font-weight: 600;
        border: 1px solid #dee2e6;
        padding: 12px 8px;
        text-align: center;
        vertical-align: middle;
    }
    .table td {
        color: #4B5563 !important; /* Abu-abu medium untuk isi tabel */
    }
    .form-label {
        color: #7C3AED !important; /* Ungu untuk label */
        font-weight: 500;
    }
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .stats-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .stats-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
</style>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 page-title">Laporan Presensi Siswa</h1>
    <div>
        <a href="{{ route('admin.laporan.exportExcel', array_filter(['tanggal_mulai' => $tanggal_mulai, 'tanggal_akhir' => $tanggal_akhir, 'semester' => $semester, 'tahun_ajaran' => $tahun_ajaran, 'kelas' => request('kelas')])) }}" class="btn btn-success me-2">Download Excel</a>
        <a href="{{ route('admin.laporan.exportPdf', array_filter(['tanggal_mulai' => $tanggal_mulai, 'tanggal_akhir' => $tanggal_akhir, 'semester' => $semester, 'tahun_ajaran' => $tahun_ajaran, 'kelas' => request('kelas')])) }}" class="btn btn-danger">Download PDF</a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control" value="{{ $tanggal_mulai ?? '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" class="form-control" value="{{ $tanggal_akhir ?? '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Semester</label>
                <select name="semester" class="form-select" id="semester">
                    <option value="">Pilih Semester</option>
                    <option value="1" {{ $semester == '1' ? 'selected' : '' }}>Semester 1</option>
                    <option value="2" {{ $semester == '2' ? 'selected' : '' }}>Semester 2</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tahun Ajaran</label>
                <select name="tahun_ajaran" class="form-select" id="tahun_ajaran">
                    <option value="">Pilih Tahun</option>
                    @for($i = date('Y')-2; $i <= date('Y')+1; $i++)
                        <option value="{{ $i }}/{{ $i+1 }}" {{ $tahun_ajaran == $i.'/'.($i+1) ? 'selected' : '' }}>{{ $i }}/{{ $i+1 }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Filter Kelas</label>
                <select name="kelas" class="form-select">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k }}" {{ request('kelas') == $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Cari Nama/NISN</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nama atau NISN">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
                <a href="{{ route('admin.laporan.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

@if($tanggal_mulai || $semester)
<div class="row mb-4">
    <div class="col-md-2">
        <div class="stats-card text-center">
            <div class="stats-number">{{ $total_siswa }}</div>
            <div class="stats-label">Total Siswa</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stats-card text-center" style="background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);">
            <div class="stats-number">{{ $tepat_waktu }}</div>
            <div class="stats-label">Tepat Waktu</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stats-card text-center" style="background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);">
            <div class="stats-number">{{ $terlambat }}</div>
            <div class="stats-label">Terlambat</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stats-card text-center" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
            <div class="stats-number">{{ $sakit }}</div>
            <div class="stats-label">Sakit</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stats-card text-center" style="background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);">
            <div class="stats-number">{{ $izin }}</div>
            <div class="stats-label">Izin</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stats-card text-center" style="background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);">
                            <div class="stats-number">{{ $alpa }}</div>
                <div class="stats-label">Alpa</div>
        </div>
    </div>
</div>
@endif

<div class="card">
    <div class="card-body">
        <h5 class="mb-3">
            Rekap Presensi
            @if($semester && $tahun_ajaran)
                - Semester {{ $semester }} Tahun Ajaran {{ $tahun_ajaran }}
            @elseif($tanggal_mulai && $tanggal_akhir)
                - {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tanggal_akhir)->format('d/m/Y') }}
            @endif
        </h5>
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Kelas</th>
                        <th>Tanggal</th>
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
                            <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ $p->waktu_scan ? \Carbon\Carbon::parse($p->waktu_scan)->format('H:i') : '-' }}</td>
                            <td>
                                {{ $p->jam_pulang ? \Carbon\Carbon::parse($p->jam_pulang)->format('H:i') : '-' }}
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
                                @elseif($p->status == 'alpa')
<span class="badge bg-secondary">Alpa</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($p->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $jam = isset($p->waktu_scan) ? \Carbon\Carbon::parse($p->waktu_scan)->format('H:i') : '-';
                                @endphp
                                @if($p->status === 'terlambat')
                                    Datang pukul {{ $jam }}, melewati jam masuk 07:30
                                @elseif($p->status === 'tepat_waktu')
                                    Datang pukul {{ $jam }}, sesuai waktu kedatangan
                                @elseif($p->status === 'izin')
                                    {{ $p->keterangan ?? '-' }}
                                @elseif($p->status === 'sakit')
                                    Izin sakit, surat diserahkan ke TU
                                @elseif($p->status === 'alpa')
                                    Tidak hadir tanpa keterangan
                                @else
                                    {{ $p->keterangan ?? '-' }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Belum ada data presensi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const semesterSelect = document.getElementById('semester');
    const tahunAjaranSelect = document.getElementById('tahun_ajaran');
    const tanggalMulaiInput = document.querySelector('input[name="tanggal_mulai"]');
    const tanggalAkhirInput = document.querySelector('input[name="tanggal_akhir"]');
    
    // Ketika semester dipilih, disable input tanggal
    semesterSelect.addEventListener('change', function() {
        if (this.value) {
            tanggalMulaiInput.disabled = true;
            tanggalAkhirInput.disabled = true;
            tanggalMulaiInput.value = '';
            tanggalAkhirInput.value = '';
        } else {
            tanggalMulaiInput.disabled = false;
            tanggalAkhirInput.disabled = false;
        }
    });
    
    // Ketika tanggal diisi, disable semester
    tanggalMulaiInput.addEventListener('change', function() {
        if (this.value) {
            semesterSelect.disabled = true;
            tahunAjaranSelect.disabled = true;
            semesterSelect.value = '';
            tahunAjaranSelect.value = '';
        } else {
            semesterSelect.disabled = false;
            tahunAjaranSelect.disabled = false;
        }
    });
    
    tanggalAkhirInput.addEventListener('change', function() {
        if (this.value) {
            semesterSelect.disabled = true;
            tahunAjaranSelect.disabled = true;
            semesterSelect.value = '';
            tahunAjaranSelect.value = '';
        } else {
            semesterSelect.disabled = false;
            tahunAjaranSelect.disabled = false;
        }
    });
    
    // Set initial state
    if (semesterSelect.value) {
        tanggalMulaiInput.disabled = true;
        tanggalAkhirInput.disabled = true;
    }
    if (tanggalMulaiInput.value) {
        semesterSelect.disabled = true;
        tahunAjaranSelect.disabled = true;
    }
});
</script>
@endsection 