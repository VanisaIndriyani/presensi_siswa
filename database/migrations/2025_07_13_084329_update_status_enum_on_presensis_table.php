<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE presensis MODIFY status ENUM('tepat_waktu','terlambat','izin','sakit') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE presensis MODIFY status ENUM('tepat_waktu','terlambat') NOT NULL");
    }
};
