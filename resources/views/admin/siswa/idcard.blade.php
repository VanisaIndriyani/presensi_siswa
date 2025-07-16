<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card - {{ $siswa->nama }}</title>
    <style>
        body {
            background: #e6f0fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .idcard-simple {
            width: 340px;
            height: 340px; /* dari 440px */
            background: #f4faff;
            border-radius: 14px;
            box-shadow: 0 6px 24px rgba(44, 62, 80, 0.13);
            border: 2.5px solid #b6d4fa;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 18px;
            position: relative;
            justify-content: center;
        }
        .idcard-title {
            width: 100%;
            background: #b6d4fa;
            color: #1e293b;
            text-align: center;
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 18px 0 10px 0;
            border-radius: 12px 12px 0 0;
        }
        .idcard-qr {
            margin: 12px 0 8px 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .idcard-qr svg {
            width: 120px;
            height: 120px;
        }
        .idcard-info {
            width: 100%;
            text-align: center;
            margin-bottom: 6px;
        }
        .idcard-nama {
            font-size: 1.1rem;
            font-weight: 700;
            color: #22223b;
            margin-bottom: 2px;
            letter-spacing: 1px;
        }
        .idcard-nisn {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 2px;
            letter-spacing: 1px;
        }
        .idcard-kelas {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2563eb;
            margin-bottom: 2px;
        }
        .idcard-note {
            position: absolute;
            bottom: 18px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 0.92rem;
            color: #64748b;
            font-style: italic;
            letter-spacing: 0.5px;
        }
        @media print {
            body { background: #fff; }
            .idcard-simple { box-shadow: none; border: 2px solid #b6d4fa; }
            .print-btn, .back-btn { display: none; }
        }
        .print-btn, .back-btn {
            position: fixed;
            z-index: 99;
            top: 24px;
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(44, 62, 80, 0.13);
            transition: all 0.2s;
        }
        .print-btn {
            right: 32px;
            background: #2563eb;
            color: #fff;
        }
        .print-btn:hover { background: #1d4ed8; }
        .back-btn {
            left: 32px;
            background: #6b7280;
            color: #fff;
        }
        .back-btn:hover { background: #374151; }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()"><i class="fas fa-print"></i> Cetak</button>
    <a href="{{ route('admin.siswa.index') }}" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali</a>
    <div class="idcard-simple">
        <div class="idcard-title">PRESENSI SISWA</div>
        <div class="idcard-qr">{!! $qrCode !!}</div>
        <div class="idcard-info">
            <div class="idcard-nama">{{ strtoupper($siswa->nama) }}</div>
            <div class="idcard-nisn">{{ $siswa->nisn }}</div>
            <div class="idcard-kelas">Kelas {{ $siswa->kelas }}</div>
        </div>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>
