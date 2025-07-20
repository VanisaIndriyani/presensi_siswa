<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|unique:gurus,nip',
            'email' => 'required|email|unique:gurus,email|unique:users,email',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'password' => 'required|min:6|max:255',
        ]);

        try {
            // Buat user account untuk guru
            $user = User::create([
                'name' => $validated['nama'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'guru',
                'email_verified_at' => now(),
            ]);

            // Buat record guru di tabel gurus
            $guru = Guru::create([
                'nama' => $validated['nama'],
                'nip' => $validated['nip'],
                'email' => $validated['email'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
            ]);

            // Simpan password sementara di session untuk ditampilkan
            session()->flash('new_guru_password', $validated['password']);
            session()->flash('new_guru_email', $validated['email']);

            return redirect()->route('admin.guru.index')->with('success', 'Guru dan akun berhasil ditambahkan!');
        } catch (\Exception $e) {
            // Jika terjadi error, hapus user yang sudah dibuat (jika ada)
            if (isset($user)) {
                $user->delete();
            }
            return back()->withErrors(['email' => 'Terjadi kesalahan saat membuat akun. Silakan coba lagi.'])->withInput();
        }
    }

    public function show(Guru $guru)
    {
        return response()->json($guru);
    }

    public function checkAccount(Guru $guru)
    {
        $user = User::where('email', $guru->email)->first();
        return response()->json([
            'has_account' => $user ? true : false,
            'email' => $guru->email
        ]);
    }

    public function edit(Guru $guru)
    {
        return response()->json($guru);
    }

    public function update(Request $request, Guru $guru)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|unique:gurus,nip,' . $guru->id,
            'email' => 'required|email|unique:gurus,email,' . $guru->id,
            'mapel' => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string|max:500',
            'password' => 'nullable|min:6|max:255',
        ]);

        // Update data guru
        $guru->update([
            'nama' => $validated['nama'],
            'nip' => $validated['nip'],
            'email' => $validated['email'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
        ]);

        // Update user account jika email berubah atau password diisi
        $oldEmail = $guru->getOriginal('email');
        $newEmail = $validated['email'];
        
        $user = User::where('email', $oldEmail)->first();
        if ($user) {
            $userData = [
                'name' => $validated['nama'],
                'email' => $newEmail,
            ];
            
            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }
            
            $user->update($userData);
        }

        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil diupdate!');
    }

    public function destroy(Guru $guru)
    {
        // Hapus user account juga
        $user = User::where('email', $guru->email)->first();
        if ($user) {
            $user->delete();
        }
        
        $guru->delete();
        return redirect()->route('admin.guru.index')->with('success', 'Guru dan akun berhasil dihapus!');
    }
}
