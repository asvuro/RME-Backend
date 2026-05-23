<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Port `aplikasi.properti_config` simgos2 → kunci bernama.
     * Dipakai HospitalConfig (gerbang admission, lock tagihan, izin order, dst).
     */
    public function up(): void
    {
        Schema::create('rs_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type', 10)->default('string'); // string|json|int|bool
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rs_settings');
    }
};
