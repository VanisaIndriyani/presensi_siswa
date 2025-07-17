<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PresensiExport;
use PDF;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $search = $request->input('search');
        $presensis = Presensi::with('siswa')
            ->when($tanggal, function($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal);
            })
            ->when($search, function($q) use ($search) {
                $q->whereHas('siswa', function($qs) use ($search) {
                    $qs->where('nama', 'like', "%$search%")
                       ->orWhere('nisn', 'like', "%$search%") ;
                });
            })
            ->orderByDesc('waktu_scan')
            ->get();
        return view('admin.laporan.index', compact('presensis', 'tanggal'));
    }

    public function exportExcel(Request $request)
    {
        $tanggal = $request->input('tanggal');
        return Excel::download(new PresensiExport($tanggal), 'laporan-presensi.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $presensis = Presensi::with('siswa')
            ->when($tanggal, function($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal);
            })
            ->orderByDesc('waktu_scan')
            ->get();
        $pdf = PDF::loadView('admin.laporan.pdf', compact('presensis', 'tanggal'));
        return $pdf->download('laporan-presensi.pdf');
    }
}
