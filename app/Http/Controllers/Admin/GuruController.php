<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::orderBy('nama')->get();
        return view('admin.guru.index', compact('gurus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'nip' => 'required|unique:gurus,nip',
            'email' => 'required|email|unique:gurus,email',
            
            'jenis_kelamin' => 'nullable',
         
        ]);
        Guru::create($validated);
        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil ditambahkan!');
    }

    public function show(Guru $guru)
    {
        return response()->json($guru);
    }

    public function edit(Guru $guru)
    {
        return response()->json($guru);
    }

    public function update(Request $request, Guru $guru)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'nip' => 'required|unique:gurus,nip,' . $guru->id,
            'email' => 'required|email|unique:gurus,email,' . $guru->id,
            'mapel' => 'nullable',
            'jenis_kelamin' => 'nullable',
            'alamat' => 'nullable',
        ]);
        $guru->update($validated);
        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil diupdate!');
    }

    public function destroy(Guru $guru)
    {
        $guru->delete();
        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil dihapus!');
    }
}
