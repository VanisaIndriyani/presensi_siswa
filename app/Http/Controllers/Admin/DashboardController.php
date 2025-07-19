<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Presensi;
use App\Models\Libur;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalSiswa = Siswa::count();
        $hadirHariIni = Presensi::whereDate('tanggal', today())->count();
        $terlambatHariIni = Presensi::whereDate('tanggal', today())
            ->where('status', 'terlambat')
            ->count();
        $sakitHariIni = Presensi::whereDate('tanggal', today())->where('status', 'sakit')->count();
        $izinHariIni = Presensi::whereDate('tanggal', today())->where('status', 'izin')->count();
        $alfaHariIni = Presensi::whereDate('tanggal', today())->where('status', 'alfa')->count();

        // Filter grafik
        $range = $request->input('range', '7hari');
        $labels = [];
        $hadirData = [];
        $terlambatData = [];
        $absenData = [];
        $sakitData = [];
        $izinData = [];
        $alfaData = [];

        if ($range === 'minggu') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
            $period = \Carbon\CarbonPeriod::create($start, $end);
            foreach ($period as $date) {
                $labels[] = $date->isoFormat('ddd');
                $hadir = Presensi::whereDate('tanggal', $date->format('Y-m-d'))->where('status', 'tepat_waktu')->count();
                $terlambat = Presensi::whereDate('tanggal', $date->format('Y-m-d'))->where('status', 'terlambat')->count();
                $sakit = Presensi::whereDate('tanggal', $date->format('Y-m-d'))->where('status', 'sakit')->count();
                $izin = Presensi::whereDate('tanggal', $date->format('Y-m-d'))->where('status', 'izin')->count();
                $absen = $totalSiswa - $hadir - $terlambat - $sakit - $izin;
                $alfa = Presensi::whereDate('tanggal', $date->format('Y-m-d'))->where('status', 'alfa')->count();
                $hadirData[] = $hadir;
                $terlambatData[] = $terlambat;
                $sakitData[] = $sakit;
                $izinData[] = $izin;
                $absenData[] = $absen;
                $alfaData[] = $alfa;
            }
        } elseif ($range === 'bulan') {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now();
            $period = \Carbon\CarbonPeriod::create($start, $end);
            foreach ($period as $date) {
                $labels[] = $date->isoFormat('D MMM');
                $hadir = Presensi::whereDate('tanggal', $date->format('Y-m-d'))->where('status', 'tepat_waktu')->count();
                $terlambat = Presensi::whereDate('tanggal', $date->format('Y-m-d'))->where('status', 'terlambat')->count();
                $sakit = Presensi::whereDate('tanggal', $date->format('Y-m-d'))->where('status', 'sakit')->count();
                $izin = Presensi::whereDate('tanggal', $date->format('Y-m-d'))->where('status', 'izin')->count();
                $absen = $totalSiswa - $hadir - $terlambat - $sakit - $izin;
                $alfa = Presensi::whereDate('tanggal', $date->format('Y-m-d'))->where('status', 'alfa')->count();
                $hadirData[] = $hadir;
                $terlambatData[] = $terlambat;
                $sakitData[] = $sakit;
                $izinData[] = $izin;
                $absenData[] = $absen;
                $alfaData[] = $alfa;
            }
        } else {
            for ($i = 6; $i >= 0; $i--) {
                $tanggal = Carbon::now()->subDays($i)->format('Y-m-d');
                $labels[] = Carbon::now()->subDays($i)->isoFormat('D MMM');
                $hadir = Presensi::whereDate('tanggal', $tanggal)->where('status', 'tepat_waktu')->count();
                $terlambat = Presensi::whereDate('tanggal', $tanggal)->where('status', 'terlambat')->count();
                $sakit = Presensi::whereDate('tanggal', $tanggal)->where('status', 'sakit')->count();
                $izin = Presensi::whereDate('tanggal', $tanggal)->where('status', 'izin')->count();
                $absen = $totalSiswa - $hadir - $terlambat - $sakit - $izin;
                $alfa = Presensi::whereDate('tanggal', $tanggal)->where('status', 'alfa')->count();
                $hadirData[] = $hadir;
                $terlambatData[] = $terlambat;
                $sakitData[] = $sakit;
                $izinData[] = $izin;
                $absenData[] = $absen;
                $alfaData[] = $alfa;
            }
        }

        $liburHariIni = \App\Models\Libur::where('tanggal', today())->first();
        return view('admin.dashboard', [
            'totalSiswa' => $totalSiswa,
            'hadirHariIni' => $hadirHariIni,
            'terlambatHariIni' => $terlambatHariIni,
            'sakitHariIni' => $sakitHariIni,
            'izinHariIni' => $izinHariIni,
            'alfaHariIni' => $alfaHariIni,
            'labels' => $labels,
            'hadirData' => $hadirData,
            'terlambatData' => $terlambatData,
            'sakitData' => $sakitData,
            'izinData' => $izinData,
            'absenData' => $absenData,
            'alfaData' => $alfaData,
            'range' => $range,
            'liburHariIni' => $liburHariIni,
        ]);
    }
}
