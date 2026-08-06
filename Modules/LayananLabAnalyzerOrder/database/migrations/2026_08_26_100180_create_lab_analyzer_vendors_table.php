<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_analyzer_vendors', function (Blueprint $table) {
            $table->id();
            // Novanet / Vanslab / Winacom - nama driver LIS pihak ketiga.
            // Bridging protokol aslinya TIDAK dibuat di modul ini; vendor hanya
            // katalog referensi + catatan koneksi untuk modul interfacing nyata nanti.
            $table->string('vendor_name')->unique();
            $table->text('connection_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_analyzer_vendors');
    }
};
