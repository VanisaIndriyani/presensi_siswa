<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KepalaSekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah user kepala sekolah sudah ada
        $existingUser = User::where('email', 'kepalasekolah@gmail.com')->first();
        
        if (!$existingUser) {
            User::create([
                'name' => 'Kepala Sekolah',
                'email' => 'kepalasekolah@gmail.com',
                'password' => Hash::make('kepalasekolah123'),
                'role' => 'kepala_sekolah',
            ]);
            
            $this->command->info('Kepala Sekolah berhasil dibuat!');
            $this->command->info('Email: kepalasekolah@gmail.com');
            $this->command->info('Password: kepalasekolah123');
        } else {
            $this->command->info('Kepala Sekolah sudah ada di database.');
            $this->command->info('Email: kepalasekolah@gmail.com');
            $this->command->info('Password: kepalasekolah123');
        }
    }
}
