<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Libur;
use App\Models\JamMasuk;
use Illuminate\Http\Request;

class LiburController extends Controller
{
    public function index()
    {
        $liburs = Libur::orderBy('tanggal')->get();
        $jamMasuk = JamMasuk::first();
        return view('admin.libur.index', compact('liburs', 'jamMasuk'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date|unique:liburs,tanggal',
            'keterangan' => 'required|string|max:255',
        ]);
        Libur::create($validated);
        return redirect()->route('admin.libur.index')->with('success', 'Hari libur berhasil ditambahkan!');
    }

    public function show(Libur $libur)
    {
        return response()->json($libur);
    }

    public function edit(Libur $libur)
    {
        return response()->json($libur);
    }

    public function update(Request $request, Libur $libur)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date|unique:liburs,tanggal,' . $libur->id,
            'keterangan' => 'required|string|max:255',
        ]);
        $libur->update($validated);
        return redirect()->route('admin.libur.index')->with('success', 'Hari libur berhasil diupdate!');
    }

    public function destroy(Libur $libur)
    {
        $libur->delete();
        return redirect()->route('admin.libur.index')->with('success', 'Hari libur berhasil dihapus!');
    }

    public function updateJamMasuk(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'jam_pulang_minimal' => 'required|date_format:H:i',
            'selisih_jam_minimal' => 'required|integer|min:1|max:12',
        ], [
            'start_time.required' => 'Jam mulai masuk harus diisi',
            'start_time.date_format' => 'Format jam mulai tidak valid',
            'end_time.required' => 'Jam tutup masuk harus diisi',
            'end_time.date_format' => 'Format jam tutup tidak valid',
            'jam_pulang_minimal.required' => 'Jam pulang minimal harus diisi',
            'jam_pulang_minimal.date_format' => 'Format jam pulang minimal tidak valid',
            'selisih_jam_minimal.required' => 'Selisih jam minimal harus diisi',
            'selisih_jam_minimal.integer' => 'Selisih jam minimal harus berupa angka',
            'selisih_jam_minimal.min' => 'Selisih jam minimal minimal 1 jam',
            'selisih_jam_minimal.max' => 'Selisih jam minimal maksimal 12 jam',
        ]);

        // Validasi manual untuk memastikan end_time setelah start_time
        if ($request->start_time >= $request->end_time) {
            return back()->withErrors(['end_time' => 'Jam tutup harus setelah jam mulai'])->withInput();
        }

        // Validasi jam pulang minimal harus setelah jam tutup masuk
        if ($request->end_time >= $request->jam_pulang_minimal) {
            return back()->withErrors(['jam_pulang_minimal' => 'Jam pulang minimal harus setelah jam tutup masuk'])->withInput();
        }

        $jamMasuk = JamMasuk::first();
        if (!$jamMasuk) {
            JamMasuk::create($request->only('start_time', 'end_time', 'jam_pulang_minimal', 'selisih_jam_minimal'));
        } else {
            $jamMasuk->update($request->only('start_time', 'end_time', 'jam_pulang_minimal', 'selisih_jam_minimal'));
        }
        
        return redirect()->route('admin.libur.index')->with('success', 'Pengaturan jam presensi berhasil diupdate!');
    }
}
