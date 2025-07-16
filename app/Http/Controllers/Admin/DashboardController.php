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

        // Filter grafik
        $range = $request->input('range', '7hari');
        $labels = [];
        $hadirData = [];
        $terlambatData = [];
        $absenData = [];

        if ($range === 'minggu') {
            // Senin - Minggu minggu ini
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
            $period = \Carbon\CarbonPeriod::create($start, $end);
            foreach ($period as $date) {
                $labels[] = $date->isoFormat('ddd');
                $hadir = Presensi::whereDate('tanggal', $date->format('Y-m-d'))->where('status', 'tepat_waktu')->count();
                $terlambat = Presensi::whereDate('tanggal', $date->format('Y-m-d'))->where('status', 'terlambat')->count();
                $absen = $totalSiswa - $hadir - $terlambat;
                $hadirData[] = $hadir;
                $terlambatData[] = $terlambat;
                $absenData[] = $absen;
            }
        } elseif ($range === 'bulan') {
            // Tanggal 1 - hari ini bulan ini
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now();
            $period = \Carbon\CarbonPeriod::create($start, $end);
            foreach ($period as $date) {
                $labels[] = $date->isoFormat('D MMM');
                $hadir = Presensi::whereDate('tanggal', $date->format('Y-m-d'))->where('status', 'tepat_waktu')->count();
                $terlambat = Presensi::whereDate('tanggal', $date->format('Y-m-d'))->where('status', 'terlambat')->count();
                $absen = $totalSiswa - $hadir - $terlambat;
                $hadirData[] = $hadir;
                $terlambatData[] = $terlambat;
                $absenData[] = $absen;
            }
        } else {
            // Default: 7 hari terakhir
            for ($i = 6; $i >= 0; $i--) {
                $tanggal = Carbon::now()->subDays($i)->format('Y-m-d');
                $labels[] = Carbon::now()->subDays($i)->isoFormat('D MMM');
                $hadir = Presensi::whereDate('tanggal', $tanggal)->where('status', 'tepat_waktu')->count();
                $terlambat = Presensi::whereDate('tanggal', $tanggal)->where('status', 'terlambat')->count();
                $absen = $totalSiswa - $hadir - $terlambat;
                $hadirData[] = $hadir;
                $terlambatData[] = $terlambat;
                $absenData[] = $absen;
            }
        }

        $liburHariIni = \App\Models\Libur::where('tanggal', today())->first();
        return view('admin.dashboard', [
            'totalSiswa' => $totalSiswa,
            'hadirHariIni' => $hadirHariIni,
            'terlambatHariIni' => $terlambatHariIni,
            'labels' => $labels,
            'hadirData' => $hadirData,
            'terlambatData' => $terlambatData,
            'absenData' => $absenData,
            'range' => $range,
            'liburHariIni' => $liburHariIni,
        ]);
    }
}
