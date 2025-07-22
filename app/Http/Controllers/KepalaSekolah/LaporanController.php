<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Presensi::with(['siswa', 'guru'])
            ->join('siswas', 'presensis.siswa_id', '=', 'siswas.id');
        
        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_awal')) {
            $query->whereDate('presensis.tanggal', '>=', $request->tanggal_awal);
        }
        
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('presensis.tanggal', '<=', $request->tanggal_akhir);
        }
        
        // Filter berdasarkan kelas
        if ($request->filled('kelas')) {
            $query->where('siswas.kelas', $request->kelas);
        }
        
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('presensis.status', $request->status);
        }
        
        $presensis = $query->select('presensis.*', 'siswas.nama as nama_siswa', 'siswas.kelas')
            ->orderBy('presensis.tanggal', 'desc')
            ->orderBy('siswas.kelas')
            ->orderBy('siswas.nama')
            ->paginate(20);
        
        // Data untuk filter
        $kelasList = Siswa::distinct()->pluck('kelas')->sort();
        $statusList = ['hadir', 'tidak_hadir', 'terlambat', 'izin', 'sakit'];
        
        return view('kepala-sekolah.laporan', compact('presensis', 'kelasList', 'statusList'));
    }
    
    public function exportExcel(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PresensiExport(
                $request->tanggal_awal,
                $request->tanggal_akhir,
                $request->kelas
            ),
            'laporan-presensi-' . date('Y-m-d') . '.xlsx'
        );
    }
    
    public function exportPdf(Request $request)
    {
        $query = Presensi::with(['siswa', 'guru'])
            ->join('siswas', 'presensis.siswa_id', '=', 'siswas.id');
        
        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_awal')) {
            $query->whereDate('presensis.tanggal', '>=', $request->tanggal_awal);
        }
        
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('presensis.tanggal', '<=', $request->tanggal_akhir);
        }
        
        // Filter berdasarkan kelas
        if ($request->filled('kelas')) {
            $query->where('siswas.kelas', $request->kelas);
        }
        
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('presensis.status', $request->status);
        }
        
        $presensis = $query->select('presensis.*', 'siswas.nama as nama_siswa', 'siswas.kelas')
            ->orderBy('presensis.tanggal', 'desc')
            ->orderBy('siswas.kelas')
            ->orderBy('siswas.nama')
            ->get();
        
        $pdf = \PDF::loadView('kepala-sekolah.laporan-pdf', compact('presensis'));
        return $pdf->download('laporan-presensi-' . date('Y-m-d') . '.pdf');
    }
}
