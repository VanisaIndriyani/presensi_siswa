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
            'status' => 'required|in:tepat_waktu,terlambat,izin,sakit',
            'keterangan' => 'nullable|string',
        ]);

        // Gabungkan tanggal dan waktu
        $waktuScan = $validated['tanggal'] . ' ' . $validated['waktu_scan'];

        Presensi::create([
            'siswa_id' => $validated['siswa_id'],
            'waktu_scan' => $waktuScan,
            'status' => $validated['status'],
            'tanggal' => $validated['tanggal'],
            'keterangan' => $validated['keterangan'],
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
            'status' => 'required|in:tepat_waktu,terlambat,izin,sakit',
            'keterangan' => 'nullable|string',
        ]);

        $presensi->update($validated);
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
            'kelas' => 'required|string',
        ]);

        // Cari siswa berdasarkan NISN (karena QR code berisi NISN)
        $siswa = Siswa::where('nisn', $request->qr)->first();
        
        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau siswa tidak ditemukan!'
            ]);
        }

        // Cek apakah siswa dari kelas yang dipilih
        if ($siswa->kelas !== $request->kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak termasuk dalam kelas yang dipilih!'
            ]);
        }

        $jamMasuk = JamMasuk::first();
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
            if ($now->format('H:i') > $end) {
                return response()->json([
                    'success' => false,
                    'message' => 'Presensi sudah ditutup!'
                ]);
            }
            
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
                'message' => 'Presensi masuk berhasil!'
            ]);
        } else {
            // Sudah presensi masuk, cek apakah sudah presensi pulang
            if ($presensi->jam_pulang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa sudah melakukan presensi pulang hari ini!'
                ]);
            }
            
            // Hanya boleh presensi pulang setelah jam 07:30
            if ($now->format('H:i') < '07:30') {
                return response()->json([
                    'success' => false,
                    'message' => 'Presensi pulang hanya bisa dilakukan setelah jam 07:30!'
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
} 