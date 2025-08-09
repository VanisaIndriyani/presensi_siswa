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
        Schema::table('jam_masuks', function (Blueprint $table) {
            $table->time('jam_pulang_minimal')->default('12:00')->after('end_time');
            $table->integer('selisih_jam_minimal')->default(4)->after('jam_pulang_minimal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jam_masuks', function (Blueprint $table) {
            $table->dropColumn(['jam_pulang_minimal', 'selisih_jam_minimal']);
        });
    }
};
