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
        User::updateOrCreate(
            ['email' => 'kt6581387@gmail.com'],
            [
                'name' => 'Administrator',
                'email' => 'kt6581387@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

       

        $this->command->info('User admin dan guru berhasil dibuat!');
    }
} 