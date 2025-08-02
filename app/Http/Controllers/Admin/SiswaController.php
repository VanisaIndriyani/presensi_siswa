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
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'nisn' => 'required|string|unique:siswas,nisn|digits:10',
                'kelas' => 'required|string|max:50',
                'qr_code' => 'nullable|string|unique:siswas,qr_code',
                'jenis_kelamin' => 'nullable|string|in:Laki-laki,Perempuan',
            ], [
                'nisn.digits' => 'NISN harus terdiri dari 10 digit angka.',
                'nisn.unique' => 'NISN sudah terdaftar dalam sistem.',
                'qr_code.unique' => 'QR Code sudah digunakan.',
                'nama.required' => 'Nama siswa harus diisi.',
                'kelas.required' => 'Kelas harus dipilih.',
            ]);

            if (empty($validated['qr_code'])) {
                $validated['qr_code'] = 'QR' . uniqid() . time();
            }

            $siswa = Siswa::create($validated);
            
            return redirect()->route('admin.siswa.index')
                ->with('success', 'Siswa berhasil ditambahkan! NISN: ' . $validated['nisn']);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error creating siswa: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'])
                ->withInput();
        }
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
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'nisn' => 'required|string|unique:siswas,nisn,' . $siswa->id . '|digits:10',
                'kelas' => 'required|string|max:50',
                'qr_code' => 'nullable|string|unique:siswas,qr_code,' . $siswa->id,
                'jenis_kelamin' => 'nullable|string|in:Laki-laki,Perempuan',
            ], [
                'nisn.digits' => 'NISN harus terdiri dari 10 digit angka.',
                'nisn.unique' => 'NISN sudah terdaftar dalam sistem.',
                'qr_code.unique' => 'QR Code sudah digunakan.',
                'nama.required' => 'Nama siswa harus diisi.',
                'kelas.required' => 'Kelas harus dipilih.',
            ]);

            $siswa->update($validated);
            
            return redirect()->route('admin.siswa.index')
                ->with('success', 'Data siswa berhasil diupdate!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error updating siswa: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat mengupdate data. Silakan coba lagi.'])
                ->withInput();
        }
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
