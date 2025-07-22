<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #f3f3f3; }
    </style>
</head>
<body>
    <h2>Laporan Presensi Siswa</h2>
    @if($semester && $tahun_ajaran)
        <p><b>Periode:</b> Semester {{ $semester }} Tahun Ajaran {{ $tahun_ajaran }}</p>
    @elseif($tanggal_mulai && $tanggal_akhir)
        <p><b>Periode:</b> {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tanggal_akhir)->format('d/m/Y') }}</p>
    @endif
    @if(!empty($kelas))
        <p><b>Kelas:</b> {{ $kelas }}</p>
    @endif
    @php
        $siswaQuery = \App\Models\Siswa::query();
        if(isset($kelas) && $kelas) {
            $siswaQuery->where('kelas', $kelas);
        }
        $siswas = $siswaQuery->orderBy('nama')->get();
        $tanggalList = \App\Models\Presensi::query()
            ->when($tanggal_mulai, function($q) use ($tanggal_mulai) {
                $q->whereDate('tanggal', '>=', $tanggal_mulai);
            })
            ->when($tanggal_akhir, function($q) use ($tanggal_akhir) {
                $q->whereDate('tanggal', '<=', $tanggal_akhir);
            })
            ->when(isset($kelas) && $kelas, function($q) use ($kelas) {
                $q->whereHas('siswa', function($q) use ($kelas) {
                    $q->where('kelas', $kelas);
                });
            })
            ->distinct('tanggal')
            ->pluck('tanggal');
        $totalHari = $tanggalList->count();
        $total = ['hadir'=>0,'tidak_hadir'=>0,'izin'=>0,'sakit'=>0,'alpa'=>0];
    @endphp
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>NISN</th>
                <th>Kelas</th>
                <th>Total Hari Sekolah</th>
                <th>Hadir</th>
                <th>Tidak Hadir</th>
                <th>Izin</th>
                <th>Sakit</th>
                <th>Alpa</th>
                <th>Persentase Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswas as $i => $siswa)
                @php
                    $presensis = \App\Models\Presensi::where('siswa_id', $siswa->id)
                        ->when($tanggal_mulai, function($q) use ($tanggal_mulai) {
                            $q->whereDate('tanggal', '>=', $tanggal_mulai);
                        })
                        ->when($tanggal_akhir, function($q) use ($tanggal_akhir) {
                            $q->whereDate('tanggal', '<=', $tanggal_akhir);
                        })
                        ->get();
                    $hadir = $presensis->whereIn('status', ['tepat_waktu', 'terlambat'])->count();
                    $izin = $presensis->where('status', 'izin')->count();
                    $sakit = $presensis->where('status', 'sakit')->count();
                    $alpa = $presensis->where('status', 'alpa')->count();
                    $tidak_hadir = $izin + $sakit + $alpa;
                    $persen = $totalHari > 0 ? round(($hadir / $totalHari) * 100) : 0;
                    $total['hadir'] += $hadir;
                    $total['tidak_hadir'] += $tidak_hadir;
                    $total['izin'] += $izin;
                    $total['sakit'] += $sakit;
                    $total['alpa'] += $alpa;
                @endphp
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $siswa->nama }}</td>
                    <td>{{ $siswa->nisn }}</td>
                    <td>{{ $siswa->kelas }}</td>
                    <td>{{ $totalHari }}</td>
                    <td>{{ $hadir }}</td>
                    <td>{{ $tidak_hadir }}</td>
                    <td>{{ $izin }}</td>
                    <td>{{ $sakit }}</td>
                    <td>{{ $alpa }}</td>
                    <td>{{ $persen }}%</td>
                </tr>
            @endforeach
            <tr>
                <td></td>
                <td><b>Total</b></td>
                <td></td>
                <td></td>
                <td><b>{{ $totalHari * max(count($siswas),1) }}</b></td>
                <td><b>{{ $total['hadir'] }}</b></td>
                <td><b>{{ $total['tidak_hadir'] }}</b></td>
                <td><b>{{ $total['izin'] }}</b></td>
                <td><b>{{ $total['sakit'] }}</b></td>
                <td><b>{{ $total['alpa'] }}</b></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td><b>Rata-rata</b></td>
                <td></td>
                <td></td>
                <td><b>{{ $totalHari }}</b></td>
                <td><b>{{ count($siswas) > 0 ? round($total['hadir']/count($siswas)) : 0 }}</b></td>
                <td><b>{{ count($siswas) > 0 ? round($total['tidak_hadir']/count($siswas)) : 0 }}</b></td>
                <td><b>{{ count($siswas) > 0 ? round($total['izin']/count($siswas)) : 0 }}</b></td>
                <td><b>{{ count($siswas) > 0 ? round($total['sakit']/count($siswas)) : 0 }}</b></td>
                <td><b>{{ count($siswas) > 0 ? round($total['alpa']/count($siswas)) : 0 }}</b></td>
                <td><b>{{ $totalHari > 0 ? round(($total['hadir']/(max(count($siswas),1)*$totalHari))*100) : 0 }}%</b></td>
            </tr>
        </tbody>
    </table>
    <div style="margin-top: 30px;">
        <p><small>Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</small></p>
    </div>
</body>
</html> 