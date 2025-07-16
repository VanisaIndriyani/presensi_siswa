<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SiswaController extends Controller
{
    public function index()
    {
        $siswas = Siswa::orderBy('kelas')->orderBy('nama')->get()->groupBy('kelas');
        return view('admin.siswa.index', compact('siswas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'nisn' => 'required|unique:siswas,nisn',
            'kelas' => 'required',
            'qr_code' => 'nullable|unique:siswas,qr_code',
            'jenis_kelamin' => 'nullable',
            'alamat' => 'nullable',
        ]);
        if (empty($validated['qr_code'])) {
            $validated['qr_code'] = uniqid('QR');
        }
        Siswa::create($validated);
        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil ditambahkan!');
    }

    public function show(Siswa $siswa)
    {
        return response()->json($siswa);
    }

    public function edit(Siswa $siswa)
    {
        return response()->json($siswa);
    }

    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'nisn' => 'required|unique:siswas,nisn,' . $siswa->id,
            'kelas' => 'required',
            'qr_code' => 'nullable|unique:siswas,qr_code,' . $siswa->id,
            'jenis_kelamin' => 'nullable',
            'alamat' => 'nullable',
        ]);
        $siswa->update($validated);
        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil diupdate!');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil dihapus!');
    }

    public function idCard(Siswa $siswa)
    {
        // Generate QR code dengan NISN siswa
        $qrCode = QrCode::size(200)
            ->format('svg')
            ->generate($siswa->nisn);
        
        return view('admin.siswa.idcard', compact('siswa', 'qrCode'));
    }

    public function api(Request $request)
    {
        $query = Siswa::query();
        if ($request->kelas) {
            $query->where('kelas', $request->kelas);
        }
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('nisn', 'like', "%$search%") ;
            });
        }
        $siswas = $query->orderBy('nama')->get();
        $data = $siswas->map(function($s, $i) {
            return [
                'no' => $i+1,
                'nama' => $s->nama,
                'nisn' => $s->nisn,
                'kelas' => $s->kelas,
                'id' => $s->id,
                'qr_code' => $s->qr_code,
            ];
        });
        return response()->json($data);
    }
}
