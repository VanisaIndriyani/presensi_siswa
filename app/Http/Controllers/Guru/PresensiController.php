<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\JamMasuk;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $kelasList = Siswa::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');
        $tanggal = $request->input('tanggal', today()->format('Y-m-d'));
        $kelas = $request->input('kelas');

        $presensis = Presensi::with('siswa')
            ->when($kelas, fn($q) => $q->whereHas('siswa', fn($q2) => $q2->where('kelas', $kelas)))
            ->when($tanggal, fn($q) => $q->whereDate('tanggal', $tanggal))
            ->orderByDesc('waktu_scan')
            ->get();

        return view('guru.presensi.index', [
            'guru' => auth()->user(),
            'presensis' => $presensis,
            'tanggal' => $tanggal,
            'kelasList' => $kelasList,
        ]);
    }

    public function create()
    {
        $kelasList = Siswa::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');
        $siswas = Siswa::orderBy('nama')->get();
        return view('guru.presensi.create', compact('kelasList', 'siswas'));
    }

    public function store(Request $request)
    {
        $jamMasuk = JamMasuk::first();
        $endTime = $jamMasuk ? $jamMasuk->end_time : '08:30';
        $isLibur = \App\Models\Libur::where('tanggal', today())->exists();

        if ($isLibur) {
            return redirect()->back()->with('error', 'Hari ini libur, presensi dinonaktifkan!');
        }

        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'tanggal' => 'required|date',
            'waktu_scan' => 'required|date_format:H:i',
            'status' => 'required|in:tepat_waktu,terlambat,izin,sakit',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $siswa = Siswa::find($validated['siswa_id']);
        $existingPresensi = Presensi::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $validated['tanggal'])
            ->first();

        $waktuScan = $validated['tanggal'] . ' ' . $validated['waktu_scan'] . ':00';
        $jamTutup = Carbon::createFromFormat('H:i', $endTime);
        $status = Carbon::createFromFormat('H:i', $validated['waktu_scan'])->lte($jamTutup)
            ? $validated['status']
            : 'terlambat';

        // Jika belum presensi masuk, lakukan presensi masuk
        if (!$existingPresensi) {
            Presensi::create([
                'siswa_id' => $validated['siswa_id'],
                'kelas' => $siswa->kelas,
                'guru_id' => auth()->id(),
                'tanggal' => $validated['tanggal'],
                'waktu_scan' => $waktuScan,
                'status' => $status,
                'keterangan' => $validated['keterangan'],
            ]);
            return redirect()->route('guru.presensi.index')->with('success', 'Presensi masuk berhasil!');
        } else {
            // Sudah presensi masuk, cek apakah sudah presensi pulang
            if ($existingPresensi->jam_pulang) {
                return redirect()->route('guru.presensi.index')->with('error', 'Siswa sudah melakukan presensi pulang hari ini!');
            }
            // Hanya boleh presensi pulang setelah jam 14:00
            if ($validated['waktu_scan'] < '14:00') {
                return redirect()->route('guru.presensi.index')->with('error', 'Presensi pulang hanya bisa dilakukan setelah jam 14:00!');
            }
            $existingPresensi->update([
                'jam_pulang' => $validated['tanggal'] . ' ' . $validated['waktu_scan'] . ':00',
            ]);
            return redirect()->route('guru.presensi.index')->with('success', 'Presensi pulang berhasil!');
        }
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
            'keterangan' => 'nullable|string|max:255',
            'status' => 'required|in:tepat_waktu,terlambat,izin,sakit',
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
        \Log::info('SCAN QR REQUEST MASUK', [
            'kelas' => $request->input('kelas'),
            'qr' => $request->input('qr'),
            'auth' => auth()->user() ? auth()->user()->email : null,
        ]);

        $jamMasuk = JamMasuk::first();
        $endTimeRaw = $jamMasuk ? $jamMasuk->end_time : '08:30';

        try {
            $carbonEnd = Carbon::parse($endTimeRaw);
            $endTime = $carbonEnd->format('H:i');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Format jam masuk tidak valid. Hubungi admin.']);
        }

        $isLibur = \App\Models\Libur::where('tanggal', today())->exists();
        if ($isLibur) {
            return response()->json(['success' => false, 'message' => 'Hari ini libur, presensi dinonaktifkan!']);
        }

        $kelas = $request->input('kelas');
        $qr = $request->input('qr');
        $user = auth()->user();

        if (!$user || !$kelas || !$qr) {
            \Log::warning('DATA TIDAK LENGKAP SAAT SCAN QR', [
                'user' => $user ? $user->email : null,
                'kelas' => $kelas,
                'qr' => $qr
            ]);
            return response()->json(['success' => false, 'message' => 'Data tidak lengkap']);
        }

        $siswa = Siswa::where('qr_code', $qr)->orWhere('nisn', $qr)->first();

        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan']);
        }

        if ($siswa->kelas !== $kelas) {
            return response()->json(['success' => false, 'message' => 'Siswa tidak terdaftar di kelas ini']);
        }

        $tanggal = now()->toDateString();

        $existing = Presensi::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $tanggal)
            ->first();

        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Siswa sudah presensi hari ini']);
        }

        $waktuScan = now();
        $jamTutup = Carbon::parse($endTime);
        $status = $waktuScan->lte($jamTutup) ? 'tepat_waktu' : 'terlambat';

        $keterangan = null;
        if ($status === 'terlambat') {
            $selisihMenit = $jamTutup->diffInMinutes($waktuScan, false);
            $keterangan = 'Terlambat ' . $selisihMenit . ' menit dari jam ' . $endTime;
        }

        try {
            Presensi::create([
                'siswa_id' => $siswa->id,
                'kelas' => $kelas,
                'guru_id' => $user->id,
                'tanggal' => $tanggal,
                'waktu_scan' => $waktuScan,
                'status' => $status,
                'keterangan' => $keterangan,
            ]);

            return response()->json([
                'success' => true,
                'nama' => $siswa->nama,
                'nisn' => $siswa->nisn,
                'kelas' => $siswa->kelas,
                'waktu_scan' => $waktuScan->format('H:i:s'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan presensi: ' . $e->getMessage(),
            ]);
        }
    }

    public function api(Request $request)
    {
        $tanggal = today()->format('Y-m-d');
        $kelas = $request->input('kelas');

        $presensis = Presensi::with('siswa')
            ->when($kelas, function ($q) use ($kelas) {
                return $q->where('kelas', $kelas);
            })
            ->whereDate('tanggal', $tanggal)
            ->orderByDesc('waktu_scan')
            ->get()
            ->map(function ($p) {
                return [
                    'nama' => $p->siswa->nama,
                    'nisn' => $p->siswa->nisn,
                    'kelas' => $p->kelas,
                    'waktu_scan' => $p->waktu_scan,
                ];
            });
            
            
        return response()->json($presensis);
    }
}
