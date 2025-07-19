<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Siswa;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $query = Presensi::with('siswa')->orderByDesc('waktu_scan');
        if ($request->kelas) {
            $query->whereHas('siswa', function($q) use ($request) {
                $q->where('kelas', $request->kelas);
            });
        }
        if ($request->search) {
            $search = $request->search;
            $query->whereHas('siswa', function($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('nisn', 'like', "%$search%") ;
            });
        }
        if ($request->bulan && $request->tahun) {
            $query->whereMonth('tanggal', $request->bulan)
                  ->whereYear('tanggal', $request->tahun);
        }
        $presensis = $query->get();
        return view('admin.presensi.index', compact('presensis'));
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
            'status' => 'required|in:tepat_waktu,terlambat',
        ]);
        $presensi->update($validated);
        return redirect()->route('admin.presensi.index')->with('success', 'Presensi berhasil diupdate!');
    }

    public function destroy(Presensi $presensi)
    {
        $presensi->delete();
        return redirect()->route('admin.presensi.index')->with('success', 'Presensi berhasil dihapus!');
    }

    public function api(Request $request)
    {
        $query = Presensi::with('siswa')->orderByDesc('waktu_scan');
        if ($request->kelas) {
            $query->whereHas('siswa', function($q) use ($request) {
                $q->where('kelas', $request->kelas);
            });
        }
        if ($request->search) {
            $search = $request->search;
            $query->whereHas('siswa', function($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('nisn', 'like', "%$search%") ;
            });
        }
        $presensis = $query->get();
        // Format data untuk tabel
        $data = $presensis->map(function($p, $i) {
            return [
                'no' => $i+1,
                'nama' => $p->siswa->nama ?? '-',
                'nisn' => $p->siswa->nisn ?? '-',
                'kelas' => $p->siswa->kelas ?? '-',
                'waktu_scan' => $p->waktu_scan,
                'jam_pulang' => $p->jam_pulang,
                'status' => $p->status,
                'keterangan' => $p->keterangan,
                'id' => $p->id,
            ];
        });
        return response()->json($data);
    }
}

