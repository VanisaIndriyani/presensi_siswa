<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Guru;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah guru sudah ada
        $existingGuru = User::where('email', 'guru@sekolah.com')->first();
        
        if (!$existingGuru) {
            // Buat user account untuk guru
            $user = User::create([
                'name' => 'Guru Utama',
                'email' => 'guru@sekolah.com',
                'password' => Hash::make('guru123'),
                'role' => 'guru',
                'email_verified_at' => now(),
            ]);
            
            // Buat record guru di tabel gurus
            Guru::create([
                'nama' => 'Guru Utama',
                'nip' => '123456789',
                'email' => 'guru@sekolah.com',
                'jenis_kelamin' => 'L',
                'kelas' => 'X-A',
            ]);
            
            $this->command->info('Guru utama berhasil ditambahkan!');
            $this->command->info('Email: guru@sekolah.com');
            $this->command->info('Password: guru123');
        } else {
            $this->command->info('Guru utama sudah ada di database.');
        }
    }
} 