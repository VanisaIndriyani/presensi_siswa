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
                
                // 85% kemungkinan hadir, 15% tidak hadir
                $randomNumber = rand(1, 100);
                if ($randomNumber <= 85) {
                    // Generate jam_pulang antara 13:00 - 15:00
                    $jamPulang = $tanggal->copy()->setTime(rand(13, 15), rand(0, 59), rand(0, 59));
                    Presensi::create([
                        'siswa_id' => $siswa->id,
                        'tanggal' => $tanggal->format('Y-m-d'),
                        'waktu_scan' => $waktuScan,
                        'jam_pulang' => $jamPulang,
                        'status' => $status,
                        'keterangan' => $status === 'terlambat' ? 'Terlambat masuk sekolah' : null,
                    ]);
                } else {
                    // 15% tidak hadir - variasi status
                    $statusTidakHadir = ['alpa', 'sakit', 'izin'];
                    $randomStatus = $statusTidakHadir[array_rand($statusTidakHadir)];
                    
                    $keterangan = match($randomStatus) {
                        'alpa' => 'Tidak hadir tanpa keterangan',
                        'sakit' => 'Sakit',
                        'izin' => 'Izin',
                        default => 'Tidak hadir'
                    };
                    
                    Presensi::create([
                        'siswa_id' => $siswa->id,
                        'tanggal' => $tanggal->format('Y-m-d'),
                        'waktu_scan' => $tanggal->copy()->setTime(0, 0, 0),
                        'status' => $randomStatus,
                        'keterangan' => $keterangan,
                    ]);
                }
            }
        }



        $this->command->info('Presensi berhasil dibuat!');
    }
} 