<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            $table->string('kelas')->nullable()->after('siswa_id');
            $table->unsignedBigInteger('guru_id')->nullable()->after('kelas');
            $table->foreign('guru_id')->references('id')->on('gurus')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            $table->dropForeign(['guru_id']);
            $table->dropColumn(['kelas', 'guru_id']);
        });
    }
};
