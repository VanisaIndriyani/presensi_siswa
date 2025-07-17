<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua siswa (tanpa filter kelas)
        $siswas = \App\Models\Siswa::orderBy('nama')->get();
        $tanggal = $request->input('tanggal', today()->format('Y-m-d'));
        $presensiHariIni = \App\Models\Presensi::with('siswa')
            ->whereDate('tanggal', $tanggal)
            ->get();
        // Hitung statistik
        $totalSiswa = $siswas->count();
        $hadir = $presensiHariIni->where('status', 'tepat_waktu')->count();
        $terlambat = $presensiHariIni->where('status', 'terlambat')->count();
        $sakit = $presensiHariIni->where('status', 'sakit')->count();
        $izin = $presensiHariIni->where('status', 'izin')->count();
        $absen = $totalSiswa - $hadir - $terlambat - $sakit - $izin;
        $statistik = [
            'total_siswa' => $totalSiswa,
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'absen' => $absen,
            'sakit' => $sakit,
            'izin' => $izin,
        ];
        $guru = \App\Models\Guru::where('email', auth()->user()->email)->first();
        return view('guru.dashboard', compact('guru', 'siswas', 'presensiHariIni', 'statistik', 'tanggal'));
    }

    public function riwayat(Request $request)
    {
        $guru = Guru::where('email', auth()->user()->email)->first();
        
        if (!$guru || !$guru->kelas) {
            return view('guru.riwayat', [
                'guru' => $guru,
                'riwayatPresensi' => collect(),
                'siswaId' => null
            ]);
        }

        $siswaId = $request->input('siswa_id');
        $tanggalAwal = $request->input('tanggal_awal', now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->input('tanggal_akhir', now()->format('Y-m-d'));

        $query = Presensi::with('siswa')
            ->whereHas('siswa', function($q) use ($guru) {
                $q->where('kelas', $guru->kelas);
            })
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);

        if ($siswaId) {
            $query->where('siswa_id', $siswaId);
        }

        $riwayatPresensi = $query->orderByDesc('tanggal')->orderByDesc('waktu_scan')->get();

        $siswas = Siswa::where('kelas', $guru->kelas)->orderBy('nama')->get();

        return view('guru.riwayat', compact('guru', 'riwayatPresensi', 'siswas', 'siswaId', 'tanggalAwal', 'tanggalAkhir'));
    }

    public function pemindaiQr()
    {
        // Ambil semua kelas unik dari tabel siswa
        $kelasList = \App\Models\Siswa::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');
        return view('guru.pemindaiqr', compact('kelasList'));
    }
}
