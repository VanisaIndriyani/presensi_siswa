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
                'kelas' => 'VII-A',
                'jenis_kelamin' => 'Laki-laki',
                'qr_code' => 'QR001'
            ],
            [
                'nama' => 'Siti Nurhaliza',
                'nisn' => '1234567891',
                'kelas' => 'VII-A',
                'jenis_kelamin' => 'Perempuan',
                'qr_code' => 'QR002'
            ],
            [
                'nama' => 'Budi Santoso',
                'nisn' => '1234567892',
                'kelas' => 'VII-B',
                'jenis_kelamin' => 'Laki-laki',
                'qr_code' => 'QR003'
            ],
            [
                'nama' => 'Dewi Sartika',
                'nisn' => '1234567893',
                'kelas' => 'VII-B',
                'jenis_kelamin' => 'Perempuan',
                'qr_code' => 'QR004'
            ],
            [
                'nama' => 'Muhammad Fajar',
                'nisn' => '1234567894',
                'kelas' => 'VIII-A',
                'jenis_kelamin' => 'Laki-laki',
                'qr_code' => 'QR005'
            ],
            [
                'nama' => 'Nina Safitri',
                'nisn' => '1234567895',
                'kelas' => 'VIII-A',
                'jenis_kelamin' => 'Perempuan',
                'qr_code' => 'QR006'
            ],
            [
                'nama' => 'Rendi Pratama',
                'nisn' => '1234567896',
                'kelas' => 'VIII-B',
                'jenis_kelamin' => 'Laki-laki',
                'qr_code' => 'QR007'
            ],
            [
                'nama' => 'Putri Wulandari',
                'nisn' => '1234567897',
                'kelas' => 'VIII-B',
                'jenis_kelamin' => 'Perempuan',
                'qr_code' => 'QR008'
            ],
            [
                'nama' => 'Doni Kusuma',
                'nisn' => '1234567898',
                'kelas' => 'IX-A',
                'jenis_kelamin' => 'Laki-laki',
                'qr_code' => 'QR009'
            ],
            [
                'nama' => 'Maya Indah',
                'nisn' => '1234567899',
                'kelas' => 'IX-A',
                'jenis_kelamin' => 'Perempuan',
                'qr_code' => 'QR010'
            ],
        ];

        foreach ($siswas as $siswa) {
            Siswa::updateOrCreate(
                ['nisn' => $siswa['nisn']], // Cari berdasarkan NISN
                $siswa // Data yang akan diupdate atau dibuat
            );
        }

        $this->command->info('Siswa berhasil dibuat!');
    }
} 