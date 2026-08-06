<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mortuary_records', function (Blueprint $table) {
            $table->id();
            // Nullable: jenazah bisa berasal dari luar rawat inap (mis. DOA/dibawa
            // langsung ke kamar jenazah) sehingga tidak selalu punya kunjungan.
            $table->foreignId('visit_id')->nullable()->constrained('visits')->nullOnDelete();
            $table->foreignId('patient_id')->constrained('patients');
            $table->dateTime('admitted_at');
            $table->dateTime('released_at')->nullable();
            $table->text('cause_of_death_notes')->nullable();
            $table->string('released_to_name')->nullable();
            $table->string('released_to_relationship')->nullable();
            $table->foreignId('released_by')->nullable()->constrained('employees');
            $table->string('status')->default('in_mortuary');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mortuary_records');
    }
};
