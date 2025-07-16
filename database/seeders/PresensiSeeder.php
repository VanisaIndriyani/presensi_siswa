<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Presensi;
use App\Models\Siswa;
use Carbon\Carbon;

class PresensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua siswa
        $siswas = Siswa::all();
        
        if ($siswas->isEmpty()) {
            $this->command->info('Tidak ada siswa ditemukan. Jalankan SiswaSeeder terlebih dahulu.');
            return;
        }

        // Generate presensi untuk 7 hari terakhir
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subDays($i);
            
            foreach ($siswas as $siswa) {
                // Skip hari Sabtu dan Minggu (weekend)
                if ($tanggal->isWeekend()) {
                    continue;
                }
                
                // Random waktu scan antara 06:30 - 08:30
                $jamMasuk = $tanggal->copy()->setTime(7, 0, 0); // Jam masuk standar 07:00
                $waktuScan = $tanggal->copy()->setTime(
                    rand(6, 8), 
                    rand(0, 59), 
                    rand(0, 59)
                );
                
                // Tentukan status berdasarkan waktu scan
                $status = $waktuScan->lt($jamMasuk) ? 'tepat_waktu' : 'terlambat';
                
                // 90% kemungkinan hadir, 10% tidak hadir
                if (rand(1, 100) <= 90) {
                    Presensi::create([
                        'siswa_id' => $siswa->id,
                        'tanggal' => $tanggal->format('Y-m-d'),
                        'waktu_scan' => $waktuScan,
                        'status' => $status,
                        'keterangan' => $status === 'terlambat' ? 'Terlambat masuk sekolah' : null,
                    ]);
                }
            }
        }

        $this->command->info('Presensi berhasil dibuat!');
    }
} 