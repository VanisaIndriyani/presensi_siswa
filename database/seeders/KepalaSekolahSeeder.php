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
        User::create([
            'name' => 'Kepala Sekolah',
            'email' => 'kepalasekolah@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'kepala_sekolah',
        ]);
    }
}
