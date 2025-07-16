<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;

use App\Models\Presensi;
use App\Models\JamMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    public function store(Request $request)
    {
        $jamMasuk = JamMasuk::first();
        $end = $jamMasuk ? $jamMasuk->end_time : '08:30';
        $isLibur = \App\Models\Libur::where('tanggal', today())->exists();
        if ($isLibur) {
            return redirect()->back()->with('error', 'Hari ini libur, presensi dinonaktifkan!');
        }
        $now = now();
        $siswaId = Auth::id();
        $today = $now->toDateString();
        $presensi = Presensi::where('siswa_id', $siswaId)->whereDate('tanggal', $today)->first();
        // Jika belum presensi masuk, lakukan presensi masuk
        if (!$presensi) {
            if ($now->format('H:i') > $end) {
                return redirect()->back()->with('error', 'Presensi sudah ditutup!');
            }
            $status = ($now->format('H:i') <= $end) ? 'tepat_waktu' : 'terlambat';
            Presensi::create([
                'siswa_id' => $siswaId,
                'waktu_scan' => $now,
                'status' => $status,
                'tanggal' => $today,
                'keterangan' => $request->input('keterangan'),
            ]);
            return redirect()->back()->with('success', 'Presensi masuk berhasil!');
        } else {
            // Sudah presensi masuk, cek apakah sudah presensi pulang
            if ($presensi->jam_pulang) {
                return redirect()->back()->with('error', 'Anda sudah melakukan presensi pulang hari ini!');
            }
            // Hanya boleh presensi pulang setelah jam 14:00
            if ($now->format('H:i') < '14:00') {
                return redirect()->back()->with('error', 'Presensi pulang hanya bisa dilakukan setelah jam 14:00!');
            }
            $presensi->update([
                'jam_pulang' => $now,
            ]);
            return redirect()->back()->with('success', 'Presensi pulang berhasil!');
        }
    }
}
