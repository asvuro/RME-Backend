<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits');
            // Skor kepuasan 1-5 (1 sangat tidak puas, 5 sangat puas).
            $table->unsignedTinyInteger('satisfaction_score');
            $table->text('feedback_text')->nullable();
            $table->dateTime('submitted_at');
            $table->timestamps();

            // Satu kunjungan hanya boleh punya satu survei kepuasan.
            $table->unique('visit_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_surveys');
    }
};
