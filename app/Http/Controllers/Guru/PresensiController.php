<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\JamMasuk;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->format('Y-m-d'));
        $kelasList = Siswa::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');
        $query = Presensi::with('siswa')->orderByDesc('waktu_scan');
        if ($request->kelas) {
            $query->whereHas('siswa', function($q) use ($request) {
                $q->where('kelas', $request->kelas);
            });
        }
        if ($tanggal) {
            $query->whereDate('tanggal', $tanggal);
        }
        $presensis = $query->get();
        return view('guru.presensi.index', compact('presensis', 'tanggal', 'kelasList'));
    }

    public function create()
    {
        $kelasList = Siswa::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');
        $siswas = Siswa::orderBy('nama')->get();
        return view('guru.presensi.create', compact('kelasList', 'siswas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'tanggal' => 'required|date',
            'waktu_scan' => 'required',
            'status' => 'required|in:tepat_waktu,terlambat,izin,sakit,alpa',
            'keterangan' => 'nullable|string',
        ]);

        // Gabungkan tanggal dan waktu
        $waktuScan = $validated['tanggal'] . ' ' . $validated['waktu_scan'];

        $keterangan = null;
        if ($validated['status'] === 'izin') {
            $keterangan = $validated['keterangan'];
        } elseif ($validated['status'] === 'terlambat') {
            $keterangan = 'Terlambat masuk sekolah';
        } elseif ($validated['status'] === 'sakit') {
            $keterangan = 'Sakit, surat diserahkan ke TU';
        } elseif ($validated['status'] === 'alpa') {
            $keterangan = null;
        }
        Presensi::create([
            'siswa_id' => $validated['siswa_id'],
            'waktu_scan' => $waktuScan,
            'status' => $validated['status'],
            'tanggal' => $validated['tanggal'],
            'keterangan' => $keterangan,
        ]);

        return redirect()->route('guru.presensi.index')->with('success', 'Presensi berhasil ditambahkan!');
    }

    public function show(Presensi $presensi)
    {
        $presensi->load('siswa');
        return response()->json($presensi);
    }

    public function edit(Presensi $presensi)
    {
        $presensi->load('siswa');
        return response()->json($presensi);
    }

    public function update(Request $request, Presensi $presensi)
    {
        $validated = $request->validate([
            'status' => 'required|in:tepat_waktu,terlambat,izin,sakit,alpa',
            'keterangan' => 'nullable|string',
        ]);

        $keterangan = null;
        if ($validated['status'] === 'izin') {
            $keterangan = $validated['keterangan'];
        } elseif ($validated['status'] === 'terlambat') {
            $keterangan = 'Terlambat masuk sekolah';
        } elseif ($validated['status'] === 'sakit') {
            $keterangan = 'Sakit, surat diserahkan ke TU';
        } elseif ($validated['status'] === 'alpa') {
            $keterangan = null;
        }
        $presensi->update([
            'status' => $validated['status'],
            'keterangan' => $keterangan,
        ]);
        return redirect()->route('guru.presensi.index')->with('success', 'Presensi berhasil diupdate!');
    }

    public function destroy(Presensi $presensi)
    {
        $presensi->delete();
        return redirect()->route('guru.presensi.index')->with('success', 'Presensi berhasil dihapus!');
    }

    public function scanQr(Request $request)
    {
        $request->validate([
            'qr' => 'required|string',
        ]);

        // Cari siswa berdasarkan NISN (karena QR code berisi NISN)
        $siswa = Siswa::where('nisn', $request->qr)->first();
        
        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau siswa tidak ditemukan!'
            ]);
        }

        $jamMasuk = JamMasuk::first();
        $start = $jamMasuk ? $jamMasuk->start_time : '07:00';
        $end = $jamMasuk ? $jamMasuk->end_time : '08:30';
        $isLibur = \App\Models\Libur::where('tanggal', today())->exists();
        
        if ($isLibur) {
            return response()->json([
                'success' => false,
                'message' => 'Hari ini libur, presensi dinonaktifkan!'
            ]);
        }

        $now = now();
        $today = $now->toDateString();
        $presensi = Presensi::where('siswa_id', $siswa->id)->whereDate('tanggal', $today)->first();

        // Jika belum presensi masuk, lakukan presensi masuk
        if (!$presensi) {
            // Cek apakah sudah waktunya untuk presensi (setelah jam mulai)
            if ($now->format('H:i') < $start) {
                return response()->json([
                    'success' => false,
                    'message' => 'Presensi belum dibuka! Presensi dibuka mulai jam ' . $start
                ]);
            }
            
            // Tentukan status berdasarkan waktu
            $status = ($now->format('H:i') <= $end) ? 'tepat_waktu' : 'terlambat';
            Presensi::create([
                'siswa_id' => $siswa->id,
                'waktu_scan' => $now,
                'status' => $status,
                'tanggal' => $today,
                'keterangan' => $status === 'terlambat' ? 'Terlambat masuk sekolah' : null,
            ]);

            return response()->json([
                'success' => true,
                'nama' => $siswa->nama,
                'message' => 'Presensi masuk berhasil! Status: ' . ($status === 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat')
            ]);
        } else {
            // Sudah presensi masuk, cek apakah sudah presensi pulang
            if ($presensi->jam_pulang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa sudah melakukan presensi pulang hari ini!'
                ]);
            }
            
            // Ambil pengaturan jam pulang dari database
            $jamPulangMinimal = $jamMasuk ? $jamMasuk->jam_pulang_minimal : '12:00';
            $selisihJamMinimal = $jamMasuk ? $jamMasuk->selisih_jam_minimal : 4;
            
            // Cek apakah sudah waktunya untuk presensi pulang
            if ($now->format('H:i') < $jamPulangMinimal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Presensi pulang hanya bisa dilakukan setelah jam ' . $jamPulangMinimal . '!'
                ]);
            }
            
            // Cek apakah sudah cukup lama sejak presensi masuk
            $waktuMasuk = $presensi->waktu_scan;
            $selisihJam = $now->diffInHours($waktuMasuk);
            
            if ($selisihJam < $selisihJamMinimal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Presensi pulang hanya bisa dilakukan minimal ' . $selisihJamMinimal . ' jam setelah presensi masuk!'
                ]);
            }
            
            $presensi->update([
                'jam_pulang' => $now,
            ]);

            return response()->json([
                'success' => true,
                'nama' => $siswa->nama,
                'message' => 'Presensi pulang berhasil!'
            ]);
        }
    }

    public function api()
    {
        $presensis = Presensi::with('siswa')
            ->whereDate('tanggal', today())
            ->orderByDesc('waktu_scan')
            ->get();

        $data = $presensis->map(function($p, $i) {
            return [
                'no' => $i + 1,
                'nama' => $p->siswa->nama ?? '-',
                'nisn' => $p->siswa->nisn ?? '-',
                'kelas' => $p->siswa->kelas ?? '-',
                'waktu_scan' => $p->waktu_scan,
                'jam_pulang' => $p->jam_pulang,
                'status' => $p->status,
                'keterangan' => $p->keterangan,
            ];
        });

        return response()->json($data);
    }

    public function getSiswaByStatus($status)
    {
        try {
            $today = today()->toDateString();
            
            if ($status === 'kehadiran') {
                // Untuk kehadiran, ambil semua yang hadir (tepat_waktu + terlambat)
                $presensis = Presensi::with('siswa')
                    ->whereDate('tanggal', $today)
                    ->whereIn('status', ['tepat_waktu', 'terlambat'])
                    ->get();
            } elseif ($status === 'absen') {
                // Untuk absen, ambil siswa yang tidak ada presensi hari ini
                $siswaHadir = Presensi::whereDate('tanggal', $today)->pluck('siswa_id');
                $siswas = Siswa::whereNotIn('id', $siswaHadir)->get();
                
                $data = [];
                foreach ($siswas as $siswa) {
                    $data[] = [
                        'nama' => $siswa->nama,
                        'nisn' => $siswa->nisn,
                        'kelas' => $siswa->kelas,
                        'status' => 'Tidak Hadir'
                    ];
                }
                
                return response()->json($data);
            } else {
                // Untuk status lainnya (tepat_waktu, terlambat, sakit, izin, alpa)
                $presensis = Presensi::with('siswa')
                    ->whereDate('tanggal', $today)
                    ->where('status', $status)
                    ->get();
            }

            $data = [];
            foreach ($presensis as $p) {
                $statusText = match($p->status) {
                    'tepat_waktu' => 'Tepat Waktu',
                    'terlambat' => 'Terlambat',
                    'sakit' => 'Sakit',
                    'izin' => 'Izin',
                    'alpa' => 'Alpa',
                    default => ucfirst($p->status)
                };
                
                $data[] = [
                    'nama' => $p->siswa ? $p->siswa->nama : '-',
                    'nisn' => $p->siswa ? $p->siswa->nisn : '-',
                    'kelas' => $p->siswa ? $p->siswa->kelas : '-',
                    'status' => $statusText
                ];
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }


} 