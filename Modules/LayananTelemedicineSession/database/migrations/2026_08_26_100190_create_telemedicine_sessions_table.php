<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telemedicine_sessions', function (Blueprint $table) {
            $table->id();
            // Sesi selalu milik satu kunjungan; hilangnya kunjungan menghapus jejak sesinya.
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            // Dokter pemeriksa; restrictOnDelete agar riwayat sesi tidak ikut terhapus
            // saat data pegawai dibersihkan (jejak klinis tetap utuh).
            $table->foreignId('doctor_employee_id')->constrained('employees')->restrictOnDelete();
            $table->dateTime('scheduled_at');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            // Placeholder ruang konsultasi; integrasi video call asli belum ada,
            // service mengisi path lokal '/telemedicine/rooms/{uuid}' saat create.
            $table->string('session_url')->nullable();
            $table->string('status')->default('scheduled');
            $table->text('consultation_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telemedicine_sessions');
    }
};
