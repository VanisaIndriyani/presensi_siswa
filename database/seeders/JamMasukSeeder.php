<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JamMasuk;

class JamMasukSeeder extends Seeder
{
    public function run()
    {
        JamMasuk::create([
            'start_time' => '07:30',
            'end_time' => '08:30',
        ]);
    }
}
