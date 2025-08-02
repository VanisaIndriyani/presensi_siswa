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
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'nip' => 'required|string|unique:gurus,nip|digits:18',
                'email' => 'required|email|unique:gurus,email|unique:users,email',
                'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
                'password' => 'required|min:6|max:255',
            ], [
                'nip.digits' => 'NIP harus terdiri dari 18 digit angka.',
                'nip.unique' => 'NIP sudah terdaftar dalam sistem.',
                'email.unique' => 'Email sudah digunakan.',
                'email.email' => 'Format email tidak valid.',
                'password.min' => 'Password minimal 6 karakter.',
            ]);

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

            return redirect()->route('admin.guru.index')
                ->with('success', 'Guru dan akun berhasil ditambahkan! NIP: ' . $validated['nip']);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Jika terjadi error, hapus user yang sudah dibuat (jika ada)
            if (isset($user)) {
                $user->delete();
            }
            \Log::error('Error creating guru: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat membuat akun. Silakan coba lagi.'])
                ->withInput();
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
            'nip' => 'required|string|unique:gurus,nip,' . $guru->id . '|digits:18',
            'email' => 'required|email|unique:gurus,email,' . $guru->id,
            'mapel' => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string|max:500',
            'password' => 'nullable|min:6|max:255',
        ], [
            'nip.digits' => 'NIP harus terdiri dari 18 digit angka.',
        ]);

        // Update data guru
        $guru->update([
            'nama' => $validated['nama'],
            'nip' => $validated['nip'],
            'email' => $validated['email'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
        ]);

        // Update user account jika email berubah atau password diisi
        $user = User::where('email', $guru->getOriginal('email'))->first();
        if ($user) {
            $updateData = ['name' => $validated['nama']];
            
            if ($validated['email'] !== $guru->getOriginal('email')) {
                $updateData['email'] = $validated['email'];
            }
            
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }
            
            $user->update($updateData);
        }

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil diupdate!');
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
