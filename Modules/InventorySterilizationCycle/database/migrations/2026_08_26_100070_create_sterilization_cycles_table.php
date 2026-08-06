<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sterilization_cycles', function (Blueprint $table) {
            $table->id();
            $table->string('cycle_number')->unique();
            $table->string('machine_name');
            $table->decimal('temperature_celsius', 6, 2);
            $table->decimal('pressure_bar', 6, 2);
            $table->unsignedInteger('duration_minutes');
            $table->dateTime('started_at');
            $table->dateTime('completed_at')->nullable();
            $table->string('biological_indicator_result')->default('pending');
            $table->string('status')->default('in_process');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sterilization_cycles');
    }
};
