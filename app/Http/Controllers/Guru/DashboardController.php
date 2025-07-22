<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PresensiExport;
use PDF;

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
        $alpa = $presensiHariIni->where('status', 'alpa')->count();
        $absen = $totalSiswa - $hadir - $terlambat - $sakit - $izin;
        $statistik = [
            'total_siswa' => $totalSiswa,
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'absen' => $absen,
            'sakit' => $sakit,
            'izin' => $izin,
            'alpa' => $alpa,
        ];
        $guru = \App\Models\Guru::where('email', auth()->user()->email)->first();
        $jamMasuk = \App\Models\JamMasuk::first();
        return view('guru.dashboard', compact('guru', 'siswas', 'presensiHariIni', 'statistik', 'tanggal', 'jamMasuk'));
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

    public function laporan(Request $request)
    {
        $guru = Guru::where('email', auth()->user()->email)->first();
        
        if (!$guru || !$guru->kelas) {
            return view('guru.laporan', [
                'guru' => $guru,
                'presensis' => collect(),
                'tanggal_mulai' => null,
                'tanggal_akhir' => null,
                'semester' => null,
                'tahun_ajaran' => null,
                'total_siswa' => 0,
                'tepat_waktu' => 0,
                'terlambat' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alpa' => 0
            ]);
        }

        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_akhir = $request->input('tanggal_akhir');
        $semester = $request->input('semester');
        $tahun_ajaran = $request->input('tahun_ajaran');
        $search = $request->input('search');
        
        // Jika semester dipilih, set tanggal berdasarkan semester
        if ($semester && $tahun_ajaran) {
            $tahun = explode('/', $tahun_ajaran)[0];
            if ($semester == '1') {
                $tanggal_mulai = $tahun . '-07-01';
                $tanggal_akhir = $tahun . '-12-31';
            } else {
                $tanggal_mulai = ($tahun + 1) . '-01-01';
                $tanggal_akhir = ($tahun + 1) . '-06-30';
            }
        }
        
        $presensis = Presensi::with('siswa')
            ->whereHas('siswa', function($q) use ($guru) {
                $q->where('kelas', $guru->kelas);
            })
            ->when($tanggal_mulai, function($q) use ($tanggal_mulai) {
                $q->whereDate('tanggal', '>=', $tanggal_mulai);
            })
            ->when($tanggal_akhir, function($q) use ($tanggal_akhir) {
                $q->whereDate('tanggal', '<=', $tanggal_akhir);
            })
            ->when($search, function($q) use ($search) {
                $q->whereHas('siswa', function($qs) use ($search) {
                    $qs->where('nama', 'like', "%$search%")
                       ->orWhere('nisn', 'like', "%$search%") ;
                });
            })
            ->orderByDesc('waktu_scan')
            ->get();
            
        // Hitung statistik
        $total_siswa = $presensis->unique('siswa_id')->count();
        $tepat_waktu = $presensis->where('status', 'tepat_waktu')->count();
        $terlambat = $presensis->where('status', 'terlambat')->count();
        $sakit = $presensis->where('status', 'sakit')->count();
        $izin = $presensis->where('status', 'izin')->count();
        $alpa = $presensis->where('status', 'alpa')->count();
        
        return view('guru.laporan', compact(
            'guru',
            'presensis', 
            'tanggal_mulai', 
            'tanggal_akhir', 
            'semester',
            'tahun_ajaran',
            'total_siswa',
            'tepat_waktu',
            'terlambat',
            'sakit',
            'izin',
            'alpa'
        ));
    }

    public function exportExcel(Request $request)
    {
        $guru = Guru::where('email', auth()->user()->email)->first();
        
        if (!$guru || !$guru->kelas) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke laporan ini.');
        }

        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_akhir = $request->input('tanggal_akhir');
        $semester = $request->input('semester');
        $tahun_ajaran = $request->input('tahun_ajaran');
        
        // Jika semester dipilih, set tanggal berdasarkan semester
        if ($semester && $tahun_ajaran) {
            $tahun = explode('/', $tahun_ajaran)[0];
            if ($semester == '1') {
                $tanggal_mulai = $tahun . '-07-01';
                $tanggal_akhir = $tahun . '-12-31';
            } else {
                $tanggal_mulai = ($tahun + 1) . '-01-01';
                $tanggal_akhir = ($tahun + 1) . '-06-30';
            }
        }
        
        $filename = 'laporan-presensi-kelas-' . $guru->kelas;
        if ($semester && $tahun_ajaran) {
            $filename .= '-semester-' . $semester . '-' . $tahun_ajaran;
        } elseif ($tanggal_mulai && $tanggal_akhir) {
            $filename .= '-' . $tanggal_mulai . '-sampai-' . $tanggal_akhir;
        }
        $filename .= '.xlsx';
        
        return Excel::download(new PresensiExport($tanggal_mulai, $tanggal_akhir, $guru->kelas), $filename);
    }

    public function exportPdf(Request $request)
    {
        $guru = Guru::where('email', auth()->user()->email)->first();
        
        if (!$guru || !$guru->kelas) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke laporan ini.');
        }

        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_akhir = $request->input('tanggal_akhir');
        $semester = $request->input('semester');
        $tahun_ajaran = $request->input('tahun_ajaran');
        
        // Jika semester dipilih, set tanggal berdasarkan semester
        if ($semester && $tahun_ajaran) {
            $tahun = explode('/', $tahun_ajaran)[0];
            if ($semester == '1') {
                $tanggal_mulai = $tahun . '-07-01';
                $tanggal_akhir = $tahun . '-12-31';
            } else {
                $tanggal_mulai = ($tahun + 1) . '-01-01';
                $tanggal_akhir = ($tahun + 1) . '-06-30';
            }
        }
        
        $presensis = Presensi::with('siswa')
            ->whereHas('siswa', function($q) use ($guru) {
                $q->where('kelas', $guru->kelas);
            })
            ->when($tanggal_mulai, function($q) use ($tanggal_mulai) {
                $q->whereDate('tanggal', '>=', $tanggal_mulai);
            })
            ->when($tanggal_akhir, function($q) use ($tanggal_akhir) {
                $q->whereDate('tanggal', '<=', $tanggal_akhir);
            })
            ->orderByDesc('waktu_scan')
            ->get();
            
        $filename = 'laporan-presensi-kelas-' . $guru->kelas;
        if ($semester && $tahun_ajaran) {
            $filename .= '-semester-' . $semester . '-' . $tahun_ajaran;
        } elseif ($tanggal_mulai && $tanggal_akhir) {
            $filename .= '-' . $tanggal_mulai . '-sampai-' . $tanggal_akhir;
        }
        $filename .= '.pdf';
        
        $pdf = PDF::loadView('guru.laporan-pdf', compact('presensis', 'tanggal_mulai', 'tanggal_akhir', 'semester', 'tahun_ajaran', 'guru'));
        return $pdf->download($filename);
    }

    public function pemindaiQr()
    {
        // Ambil semua kelas unik dari tabel siswa
        $kelasList = \App\Models\Siswa::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');
        return view('guru.pemindaiqr', compact('kelasList'));
    }
}
