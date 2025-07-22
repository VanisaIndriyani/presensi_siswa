<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Presensi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .status-hadir {
            background-color: #d4edda;
            color: #155724;
        }
        .status-tidak-hadir {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-terlambat {
            background-color: #fff3cd;
            color: #856404;
        }
        .summary {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .summary h3 {
            margin-top: 0;
            color: #333;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PRESENSI SISWA</h1>
        <p>Sistem Presensi Sekolah</p>
        <p>Tanggal: {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Status</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Guru</th>
            </tr>
        </thead>
        <tbody>
            @foreach($presensis as $i => $presensi)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($presensi->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $presensi->nama_siswa }}</td>
                    <td>{{ $presensi->kelas }}</td>
                    <td class="status-{{ str_replace('_', '-', $presensi->status) }}">
                        {{ ucfirst(str_replace('_', ' ', $presensi->status)) }}
                    </td>
                    <td>
                        @if($presensi->jam_masuk)
                            {{ \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($presensi->jam_pulang)
                            {{ \Carbon\Carbon::parse($presensi->jam_pulang)->format('H:i') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $presensi->guru->nama ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h3>Ringkasan</h3>
        <p><strong>Total Data:</strong> {{ $presensis->count() }} presensi</p>
        <p><strong>Hadir:</strong> {{ $presensis->where('status', 'hadir')->count() }} siswa</p>
        <p><strong>Tidak Hadir:</strong> {{ $presensis->where('status', 'tidak_hadir')->count() }} siswa</p>
        <p><strong>Terlambat:</strong> {{ $presensis->where('status', 'terlambat')->count() }} siswa</p>
        <p><strong>Izin/Sakit:</strong> {{ $presensis->whereIn('status', ['izin', 'sakit'])->count() }} siswa</p>
    </div>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Presensi Sekolah</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html> 