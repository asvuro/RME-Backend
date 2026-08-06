<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicine_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacy_dispense_id')->constrained('pharmacy_dispenses');
            $table->string('patient_address');
            $table->foreignId('courier_employee_id')->nullable()->constrained('employees');
            $table->string('status')->default('pending');
            $table->dateTime('requested_at');
            $table->dateTime('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicine_deliveries');
    }
};
