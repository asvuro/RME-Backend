<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vital_sign_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            // NEWS2 parameter — semua wajib karena seluruh parameter ikut skoring.
            $table->unsignedSmallInteger('respiratory_rate');
            $table->unsignedTinyInteger('spo2');
            $table->unsignedSmallInteger('systolic_bp');
            $table->unsignedSmallInteger('pulse_rate');
            $table->string('consciousness_level', 20); // alert | voice | pain | unresponsive
            $table->decimal('temperature_celsius', 4, 1);
            $table->foreignId('recorded_by')->constrained('employees')->cascadeOnDelete();
            $table->dateTime('recorded_at');
            // Hasil kalkulasi NEWS2 — kolom turunan, SELALU diisi oleh
            // EwsCalculatorService saat store (bukan dari input klien).
            $table->unsignedTinyInteger('total_score')->nullable();
            $table->string('risk_level', 10)->nullable(); // rendah | sedang | tinggi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vital_sign_observations');
    }
};
