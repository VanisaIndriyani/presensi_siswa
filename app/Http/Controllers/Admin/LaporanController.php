<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PresensiExport;
use PDF;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_akhir = $request->input('tanggal_akhir');
        $semester = $request->input('semester');
        $tahun_ajaran = $request->input('tahun_ajaran');
        $search = $request->input('search');
        $kelas = $request->input('kelas');
        $kelasList = \App\Models\Siswa::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');
        
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
            ->when($kelas, function($q) use ($kelas) {
                $q->whereHas('siswa', function($qs) use ($kelas) {
                    $qs->where('kelas', $kelas);
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
        
        return view('admin.laporan.index', compact(
            'presensis', 
            'tanggal_mulai', 
            'tanggal_akhir', 
            'semester',
            'tahun_ajaran',
            'kelas', 
            'kelasList',
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
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_akhir = $request->input('tanggal_akhir');
        $semester = $request->input('semester');
        $tahun_ajaran = $request->input('tahun_ajaran');
        $kelas = $request->input('kelas');
        
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
        
        $filename = 'laporan-presensi';
        if ($semester && $tahun_ajaran) {
            $filename .= '-semester-' . $semester . '-' . $tahun_ajaran;
        } elseif ($tanggal_mulai && $tanggal_akhir) {
            $filename .= '-' . $tanggal_mulai . '-sampai-' . $tanggal_akhir;
        }
        $filename .= '.xlsx';
        
        return Excel::download(new PresensiExport($tanggal_mulai, $tanggal_akhir, $kelas), $filename);
    }

    public function exportPdf(Request $request)
    {
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_akhir = $request->input('tanggal_akhir');
        $semester = $request->input('semester');
        $tahun_ajaran = $request->input('tahun_ajaran');
        $kelas = $request->input('kelas');
        
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
            ->when($tanggal_mulai, function($q) use ($tanggal_mulai) {
                $q->whereDate('tanggal', '>=', $tanggal_mulai);
            })
            ->when($tanggal_akhir, function($q) use ($tanggal_akhir) {
                $q->whereDate('tanggal', '<=', $tanggal_akhir);
            })
            ->when($kelas, function($q) use ($kelas) {
                $q->whereHas('siswa', function($qs) use ($kelas) {
                    $qs->where('kelas', $kelas);
                });
            })
            ->orderByDesc('waktu_scan')
            ->get();
            
        $filename = 'laporan-presensi';
        if ($semester && $tahun_ajaran) {
            $filename .= '-semester-' . $semester . '-' . $tahun_ajaran;
        } elseif ($tanggal_mulai && $tanggal_akhir) {
            $filename .= '-' . $tanggal_mulai . '-sampai-' . $tanggal_akhir;
        }
        $filename .= '.pdf';
        
        $pdf = PDF::loadView('admin.laporan.pdf', compact('presensis', 'tanggal_mulai', 'tanggal_akhir', 'semester', 'tahun_ajaran', 'kelas'));
        return $pdf->download($filename);
    }
}
