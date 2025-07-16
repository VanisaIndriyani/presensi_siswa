<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Budi Santoso, S.Pd',
            'email' => 'budi.santoso@sekolah.sch.id',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        User::create([
            'name' => 'Siti Nurhaliza, S.Pd',
            'email' => 'siti.nurhaliza@sekolah.sch.id',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        $this->command->info('User admin dan guru berhasil dibuat!');
    }
} 