@extends('layouts.guru')

@section('content')
@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@php
    $jamMasuk = \App\Models\JamMasuk::first();
    $endTime = $jamMasuk ? $jamMasuk->end_time : '08:30';
    $liburHariIni = \App\Models\Libur::where('tanggal', today())->first();
    $isLibur = $liburHariIni ? true : false;
    $now = now()->format('H:i');
@endphp
@if($isLibur)
    <div class="alert alert-info mb-3">
        <b>Hari ini libur:</b> {{ $liburHariIni->keterangan }}<br>
        Presensi dinonaktifkan.
    </div>
@endif
<style>
    .qr-container {
        max-width: 420px;
        margin: 0 auto 32px auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(44,62,80,0.08);
        padding: 24px 18px 18px 18px;
        text-align: center;
    }
    .qr-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 18px;
        color: #2563eb;
    }
    #qr-reader {
        margin: 0 auto 18px auto;
    }
    .scan-result {
        font-size: 1.1rem;
        font-weight: 600;
        margin-top: 12px;
        color: #059669;
    }
    .scan-error {
        color: #dc2626;
        font-size: 1rem;
        margin-top: 10px;
    }
</style>

<div class="qr-container" @if($isLibur) style="pointer-events:none;opacity:0.5;" @endif>
    <div class="qr-title">Pemindai QR Presensi Siswa</div>
    <div class="mb-3">
        <label for="selectKelas" class="form-label">Pilih Kelas</label>
        <select id="selectKelas" class="form-select">
            <option value="">-- Pilih Kelas --</option>
            @foreach($kelasList as $kelas)
                <option value="{{ $kelas }}">{{ $kelas }}</option>
            @endforeach
        </select>
    </div>
    <div id="qr-reader" style="width:320px;"></div>
    <div id="scan-result" class="scan-result"></div>
    <div id="scan-error" class="scan-error"></div>
</div>

<!-- Toast Notifikasi -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="toastNotif" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastNotifBody">
                Notifikasi
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header bg-light fw-bold">Daftar Siswa Sudah Presensi Hari Ini</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0" id="tablePresensiGuru">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NISN</th>
                        <th>Kelas</th>
                        <th>Waktu Scan</th>
                    </tr>
                </thead>
                <tbody id="tbodyPresensiGuru">
                    <!-- Data akan diisi via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Script QR & AJAX -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const qrReader = new Html5Qrcode("qr-reader");
    const scanResult = document.getElementById('scan-result');
    const scanError = document.getElementById('scan-error');
    let lastScanned = '';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function showResult(msg, isError = false) {
        scanResult.textContent = isError ? '' : msg;
        scanError.textContent = isError ? msg : '';
    }

    function showToast(message, isError = false) {
        const toast = document.getElementById('toastNotif');
        const toastBody = document.getElementById('toastNotifBody');
        toastBody.textContent = message;
        toast.classList.remove('text-bg-primary', 'text-bg-danger', 'text-bg-success');
        toast.classList.add(isError ? 'text-bg-danger' : 'text-bg-success');
        new bootstrap.Toast(toast).show();
    }

    function refreshPresensiGuru() {
        fetch('{{ url('guru/presensi/api') }}')
            .then(res => res.json())
            .then(data => {
                let html = '';
                if (data.length === 0) {
                    html = `<tr><td colspan='5' class='text-center text-muted'>Belum ada presensi hari ini.</td></tr>`;
                } else {
                    data.forEach((p, i) => {
                        html += `<tr>
                            <td>${i + 1}</td>
                            <td>${p.nama}</td>
                            <td>${p.nisn}</td>
                            <td>${p.kelas}</td>
                            <td>${p.waktu_scan}</td>
                        </tr>`;
                    });
                }
                document.getElementById('tbodyPresensiGuru').innerHTML = html;
            });
    }

    function onScanSuccess(decodedText, decodedResult) {
        if (decodedText === lastScanned) return;

        const selectedKelas = document.getElementById('selectKelas').value;
        console.log("Decoded QR:", decodedText);
        console.log("Kelas:", selectedKelas);
        if (!selectedKelas) {
            showResult('Pilih kelas terlebih dahulu', true);
            return;
        }

        lastScanned = decodedText;
        showResult('Memproses presensi...');

        fetch('{{ url('guru/presensi/scan-qr') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ qr: decodedText, kelas: selectedKelas })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showResult('Presensi berhasil: ' + data.nama);
                showToast('Presensi berhasil: ' + data.nama);
                refreshPresensiGuru();
            } else {
                showResult(data.message || 'Presensi gagal', true);
                showToast(data.message || 'Presensi gagal', true);
            }
            setTimeout(() => { lastScanned = ''; showResult(''); }, 2000);
        })
        .catch((err) => {
            showResult('Terjadi error saat presensi', true);
            showToast('Terjadi error saat presensi', true);
            console.error('Fetch error:', err);
            setTimeout(() => { lastScanned = ''; showResult(''); }, 2000);
        });
    }

    // Hanya disable scanner jika hari ini libur
    const isLibur = @json($isLibur);
    if (isLibur) {
        return;
    }

    Html5Qrcode.getCameras().then(cameras => {
        if (cameras && cameras.length) {
            qrReader.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: 180 },
                onScanSuccess
            );
        } else {
            showResult('Kamera tidak ditemukan', true);
        }
    }).catch(() => {
        showResult('Tidak bisa mengakses kamera', true);
    });

    refreshPresensiGuru();
    setInterval(refreshPresensiGuru, 10000);
});
</script>
@endsection
