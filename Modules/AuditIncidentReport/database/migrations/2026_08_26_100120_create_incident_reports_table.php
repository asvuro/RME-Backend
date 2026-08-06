<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_reports', function (Blueprint $table) {
            $table->id();
            // Keduanya nullable: insiden boleh dilaporkan tanpa kunjungan
            // (mis. kejadian di area umum) dan tanpa pasien tertentu.
            $table->foreignId('visit_id')->nullable()->constrained('visits')->nullOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->string('incident_category', 20)->index(); // KPC|KNC|KTC|KTD|SENTINEL
            $table->text('description');
            $table->dateTime('occurred_at');
            $table->foreignId('reported_by')->nullable()->constrained('employees')->nullOnDelete();
            $table->unsignedTinyInteger('impact_score');      // 1-5
            $table->unsignedTinyInteger('probability_score'); // 1-5
            // risk_grade/sla_due_at TIDAK pernah dari input klien — dihitung
            // IncidentReportService saat create/update (matriks 5x5 standar).
            $table->string('risk_grade', 10)->index(); // BIRU|HIJAU|KUNING|MERAH
            $table->string('status', 30)->default('reported')->index();
            $table->dateTime('sla_due_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_reports');
    }
};
