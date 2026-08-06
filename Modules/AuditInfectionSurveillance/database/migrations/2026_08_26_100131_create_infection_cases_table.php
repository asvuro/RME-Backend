<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infection_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            // ISK | plebitis | IDO | VAP
            $table->string('infection_type');
            $table->dateTime('diagnosed_at');
            // Rujukan epidemiologis ke hari-alat pemicu (bila ada); nullOnDelete
            // supaya kasus tidak ikut hilang saat riwayat alat dikoreksi.
            $table->foreignId('related_device_day_id')->nullable()->constrained('device_days')->nullOnDelete();
            $table->timestamps();

            $table->index(['infection_type', 'diagnosed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infection_cases');
    }
};
