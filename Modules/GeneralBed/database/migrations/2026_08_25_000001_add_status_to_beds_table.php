<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// State machine bed ala master.ruang_kamar_tidur.STATUS simgos2 (referensi jenis 20:
// 0 nonaktif / 1 kosong / 2 dipesan / 3 terisi), dipetakan ke string agar terbaca.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beds', function (Blueprint $table) {
            $table->string('status')->default('available')->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('beds', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
