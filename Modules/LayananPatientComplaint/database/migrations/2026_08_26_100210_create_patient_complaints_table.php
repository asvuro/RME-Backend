<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_complaints', function (Blueprint $table) {
            $table->id();
            // Keduanya nullable: komplain bisa masuk tanpa kunjungan tercatat
            // (mis. keluhan via telepon) atau pasien walk-in.
            $table->foreignId('patient_id')->nullable()->constrained('patients');
            $table->foreignId('visit_id')->nullable()->constrained('visits');
            // pelayanan | fasilitas | administrasi | lainnya
            $table->string('category');
            $table->text('description');
            $table->dateTime('submitted_at');
            // baru | diproses | selesai (state machine maju, lihat PatientComplaintService)
            $table->string('status')->default('baru');
            $table->foreignId('handled_by')->nullable()->constrained('employees');
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_complaints');
    }
};
