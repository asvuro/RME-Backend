<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crossmatch_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blood_bag_id')->constrained('blood_bags')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->restrictOnDelete();
            // pos = ada aglutinasi (reaktif), neg = tidak ada aglutinasi.
            // Kompatibel HANYA bila ketiga hasil neg (lihat BloodBankService).
            $table->string('major_result');
            $table->string('minor_result');
            $table->string('auto_control');
            $table->boolean('is_compatible');
            $table->foreignId('tested_by')->nullable()->constrained('employees')->nullOnDelete();
            $table->dateTime('tested_at');
            // tested_at + 48 jam, standar masa berlaku reservasi crossmatch bank darah.
            $table->dateTime('reserved_until')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crossmatch_tests');
    }
};
