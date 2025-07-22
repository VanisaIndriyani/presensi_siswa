<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added this import for DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hapus data yang bermasalah dulu
        DB::table('presensis')->where('status', 'alfa')->delete();
        
        // Ubah enum status
        DB::statement("ALTER TABLE presensis MODIFY status ENUM('tepat_waktu','terlambat','izin','sakit','alpa') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ubah enum status kembali
        DB::statement("ALTER TABLE presensis MODIFY status ENUM('tepat_waktu','terlambat','izin','sakit','alfa') NOT NULL");
    }
};
