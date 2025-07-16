<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $siswas = [
            [
                'nama' => 'Ahmad Rizki',
                'nisn' => '1234567890',
                'kelas' => 'X-A',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Merdeka No. 123, Jakarta',
                'qr_code' => 'QR001'
            ],
            [
                'nama' => 'Siti Nurhaliza',
                'nisn' => '1234567891',
                'kelas' => 'X-A',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Sudirman No. 45, Jakarta',
                'qr_code' => 'QR002'
            ],
            [
                'nama' => 'Budi Santoso',
                'nisn' => '1234567892',
                'kelas' => 'X-B',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Thamrin No. 67, Jakarta',
                'qr_code' => 'QR003'
            ],
            [
                'nama' => 'Dewi Sartika',
                'nisn' => '1234567893',
                'kelas' => 'X-B',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Gatot Subroto No. 89, Jakarta',
                'qr_code' => 'QR004'
            ],
            [
                'nama' => 'Muhammad Fajar',
                'nisn' => '1234567894',
                'kelas' => 'XI-A',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Rasuna Said No. 12, Jakarta',
                'qr_code' => 'QR005'
            ],
            [
                'nama' => 'Nina Safitri',
                'nisn' => '1234567895',
                'kelas' => 'XI-A',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Kuningan No. 34, Jakarta',
                'qr_code' => 'QR006'
            ],
            [
                'nama' => 'Rendi Pratama',
                'nisn' => '1234567896',
                'kelas' => 'XI-B',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Senayan No. 56, Jakarta',
                'qr_code' => 'QR007'
            ],
            [
                'nama' => 'Putri Wulandari',
                'nisn' => '1234567897',
                'kelas' => 'XI-B',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Kebayoran No. 78, Jakarta',
                'qr_code' => 'QR008'
            ],
            [
                'nama' => 'Doni Kusuma',
                'nisn' => '1234567898',
                'kelas' => 'XII-A',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Mampang No. 90, Jakarta',
                'qr_code' => 'QR009'
            ],
            [
                'nama' => 'Maya Indah',
                'nisn' => '1234567899',
                'kelas' => 'XII-A',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Tebet No. 11, Jakarta',
                'qr_code' => 'QR010'
            ],
        ];

        foreach ($siswas as $siswa) {
            Siswa::create($siswa);
        }

        $this->command->info('Siswa berhasil dibuat!');
    }
} 