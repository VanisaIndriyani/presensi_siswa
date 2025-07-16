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
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);
        $jamMasuk = JamMasuk::first();
        if (!$jamMasuk) {
            JamMasuk::create($request->only('start_time', 'end_time'));
        } else {
            $jamMasuk->update($request->only('start_time', 'end_time'));
        }
        return redirect()->route('admin.libur.index')->with('success', 'Jam masuk berhasil diupdate!');
    }
}
