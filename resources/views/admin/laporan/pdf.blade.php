<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #f3f3f3; }
        .badge-success { color: #fff; background: #16a34a; padding: 2px 8px; border-radius: 4px; }
        .badge-danger { color: #fff; background: #dc2626; padding: 2px 8px; border-radius: 4px; }
        .badge-warning { color: #000; background: #fbbf24; padding: 2px 8px; border-radius: 4px; }
        .badge-info { color: #000; background: #60a5fa; padding: 2px 8px; border-radius: 4px; }
        .badge-secondary { color: #fff; background: #6b7280; padding: 2px 8px; border-radius: 4px; }
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
    
    <table>
        <thead>
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
                    <td>{{ $p->jam_pulang ? \Carbon\Carbon::parse($p->jam_pulang)->format('H:i') : '-' }}</td>
                    <td>
                        @if($p->status == 'tepat_waktu')
                            <span class="badge-success">Tepat Waktu</span>
                        @elseif($p->status == 'terlambat')
                            <span class="badge-danger">Terlambat</span>
                        @elseif($p->status == 'sakit')
                            <span class="badge-warning">Sakit</span>
                        @elseif($p->status == 'izin')
                            <span class="badge-info">Izin</span>
                        @elseif($p->status == 'alfa')
                            <span class="badge-secondary">Alfa</span>
                        @else
                            <span class="badge-secondary">{{ ucfirst($p->status) }}</span>
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
                        @elseif($p->status === 'alfa')
                            Tidak hadir tanpa keterangan
                        @else
                            {{ $p->keterangan ?? '-' }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center; color:#888;">Belum ada data presensi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top: 30px;">
        <p><small>Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</small></p>
    </div>
</body>
</html> 