<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ambulance_trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ambulance_id')->constrained('ambulances')->cascadeOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->foreignId('driver_employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('purpose');
            $table->string('origin');
            $table->string('destination');
            $table->dateTime('departed_at');
            $table->dateTime('returned_at')->nullable();
            // ongoing = sedang berjalan, completed = kembali, cancelled = batal.
            $table->string('status')->default('ongoing');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ambulance_trips');
    }
};
