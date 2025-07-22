<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Libur;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Data untuk dashboard kepala sekolah
        $today = Carbon::today();
        $range = $request->get('range', '7hari');
        
        // Total siswa
        $totalSiswa = Siswa::count();
        
        // Total guru
        $totalGuru = Guru::count();
        
        // Presensi hari ini berdasarkan status yang benar
        $hadirHariIni = Presensi::whereDate('tanggal', $today)
            ->where('status', 'tepat_waktu')
            ->count();
        
        $terlambatHariIni = Presensi::whereDate('tanggal', $today)
            ->where('status', 'terlambat')
            ->count();
        
        $sakitHariIni = Presensi::whereDate('tanggal', $today)
            ->where('status', 'sakit')
            ->count();
        
        $izinHariIni = Presensi::whereDate('tanggal', $today)
            ->where('status', 'izin')
            ->count();
        
        $alpaHariIni = Presensi::whereDate('tanggal', $today)
            ->where('status', 'alpa')
            ->count();
        
        // Cek libur hari ini
        $liburHariIni = Libur::where('tanggal', $today)->first();
        
        // Data untuk grafik berdasarkan range
        $labels = [];
        $hadirData = [];
        $terlambatData = [];
        $izinData = [];
        $sakitData = [];
        $absenData = [];
        $alpaData = [];
        
        if ($range === '7hari') {
            for ($i = 6; $i >= 0; $i--) {
                $date = $today->copy()->subDays($i);
                $labels[] = $date->format('d/m');
                
                $hadirData[] = Presensi::whereDate('tanggal', $date)
                    ->where('status', 'tepat_waktu')
                    ->count();
                $terlambatData[] = Presensi::whereDate('tanggal', $date)
                    ->where('status', 'terlambat')
                    ->count();
                $izinData[] = Presensi::whereDate('tanggal', $date)
                    ->where('status', 'izin')
                    ->count();
                $sakitData[] = Presensi::whereDate('tanggal', $date)
                    ->where('status', 'sakit')
                    ->count();
                $alpaData[] = Presensi::whereDate('tanggal', $date)
                    ->where('status', 'alpa')
                    ->count();
                
                // Absen = siswa yang tidak ada presensi
                $siswaHadir = Presensi::whereDate('tanggal', $date)->pluck('siswa_id');
                $absenData[] = Siswa::whereNotIn('id', $siswaHadir)->count();
            }
        } elseif ($range === 'minggu') {
            $startOfWeek = $today->copy()->startOfWeek();
            for ($i = 0; $i < 7; $i++) {
                $date = $startOfWeek->copy()->addDays($i);
                $labels[] = $date->format('d/m');
                
                $hadirData[] = Presensi::whereDate('tanggal', $date)
                    ->where('status', 'tepat_waktu')
                    ->count();
                $terlambatData[] = Presensi::whereDate('tanggal', $date)
                    ->where('status', 'terlambat')
                    ->count();
                $izinData[] = Presensi::whereDate('tanggal', $date)
                    ->where('status', 'izin')
                    ->count();
                $sakitData[] = Presensi::whereDate('tanggal', $date)
                    ->where('status', 'sakit')
                    ->count();
                $alpaData[] = Presensi::whereDate('tanggal', $date)
                    ->where('status', 'alpa')
                    ->count();
                
                $siswaHadir = Presensi::whereDate('tanggal', $date)->pluck('siswa_id');
                $absenData[] = Siswa::whereNotIn('id', $siswaHadir)->count();
            }
        } else { // bulan
            $startOfMonth = $today->copy()->startOfMonth();
            $daysInMonth = $today->daysInMonth;
            for ($i = 0; $i < $daysInMonth; $i++) {
                $date = $startOfMonth->copy()->addDays($i);
                $labels[] = $date->format('d/m');
                
                $hadirData[] = Presensi::whereDate('tanggal', $date)
                    ->where('status', 'tepat_waktu')
                    ->count();
                $terlambatData[] = Presensi::whereDate('tanggal', $date)
                    ->where('status', 'terlambat')
                    ->count();
                $izinData[] = Presensi::whereDate('tanggal', $date)
                    ->where('status', 'izin')
                    ->count();
                $sakitData[] = Presensi::whereDate('tanggal', $date)
                    ->where('status', 'sakit')
                    ->count();
                $alpaData[] = Presensi::whereDate('tanggal', $date)
                    ->where('status', 'alpa')
                    ->count();
                
                $siswaHadir = Presensi::whereDate('tanggal', $date)->pluck('siswa_id');
                $absenData[] = Siswa::whereNotIn('id', $siswaHadir)->count();
            }
        }
        
        return view('kepala-sekolah.dashboard', compact(
            'totalSiswa',
            'totalGuru',
            'hadirHariIni',
            'terlambatHariIni',
            'sakitHariIni',
            'izinHariIni',
            'alpaHariIni',
            'liburHariIni',
            'range',
            'labels',
            'hadirData',
            'terlambatData',
            'izinData',
            'sakitData',
            'absenData',
            'alpaData'
        ));
    }

    public function getSiswaAlpa()
    {
        $today = Carbon::today();
        
        // Ambil siswa yang alpa hari ini
        $siswaAlpa = Presensi::whereDate('tanggal', $today)
            ->where('status', 'alpa')
            ->with('siswa')
            ->get()
            ->map(function($presensi) {
                return [
                    'nama' => $presensi->siswa->nama ?? '-',
                    'nisn' => $presensi->siswa->nisn ?? '-',
                    'kelas' => $presensi->siswa->kelas ?? '-',
                    'jenis_kelamin' => $presensi->siswa->jenis_kelamin ?? '-'
                ];
            });
        
        return response()->json($siswaAlpa);
    }
}
