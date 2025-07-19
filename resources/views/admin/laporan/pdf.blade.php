<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #f3f3f3; }
        .badge-success { color: #fff; background: #16a34a; padding: 2px 8px; border-radius: 4px; }
        .badge-danger { color: #fff; background: #dc2626; padding: 2px 8px; border-radius: 4px; }
    </style>
</head>
<body>
    <h2>Laporan Presensi Siswa</h2>
    @if($tanggal)
        <p><b>Tanggal:</b> {{ $tanggal }}</p>
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
                <th>Waktu Scan</th>
                <th>Status</th>
                <th>Keterangan</th>
                <th>Jam Pulang</th>
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
                            <span class="badge-success">Tepat Waktu</span>
                        @else
                            <span class="badge-danger">Terlambat</span>
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
                    <td>
                        {{ $p->jam_pulang ? $p->jam_pulang : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; color:#888;">Belum ada data presensi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html> 