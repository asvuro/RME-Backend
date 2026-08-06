<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            // kateter_urine | infus_iv | ventilator
            $table->string('device_type');
            $table->dateTime('inserted_at');
            $table->dateTime('removed_at')->nullable();
            $table->timestamps();

            $table->index(['visit_id', 'device_type']);
            // Denominator surveilans: filter per jenis alat + rentang periode.
            $table->index(['device_type', 'inserted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_days');
    }
};
