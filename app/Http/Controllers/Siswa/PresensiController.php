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
            
            // Ambil pengaturan jam pulang dari database
            $jamPulangMinimal = $jamMasuk ? $jamMasuk->jam_pulang_minimal : '12:00';
            $selisihJamMinimal = $jamMasuk ? $jamMasuk->selisih_jam_minimal : 4;
            
            // Cek apakah sudah waktunya untuk presensi pulang
            if ($now->format('H:i') < $jamPulangMinimal) {
                return redirect()->back()->with('error', 'Presensi pulang hanya bisa dilakukan setelah jam ' . $jamPulangMinimal . '!');
            }
            
            // Cek apakah sudah cukup lama sejak presensi masuk
            $waktuMasuk = $presensi->waktu_scan;
            $selisihJam = $now->diffInHours($waktuMasuk);
            
            if ($selisihJam < $selisihJamMinimal) {
                return redirect()->back()->with('error', 'Presensi pulang hanya bisa dilakukan minimal ' . $selisihJamMinimal . ' jam setelah presensi masuk!');
            }
            
            $presensi->update([
                'jam_pulang' => $now,
            ]);
            return redirect()->back()->with('success', 'Presensi pulang berhasil!');
        }
    }
} 