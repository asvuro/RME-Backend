<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imaging_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits');
            // Adapter tracking tanpa protokol DICOM sungguhan: modality cuma
            // label pemesanan (X-Ray | CT | MRI | USG), bukan hasil negosiasi SOP class.
            $table->string('modality');
            $table->string('body_part');
            // Nullable di level kolom supaya data impor lama tidak macet;
            // API tetap mewajibkannya lewat validasi (akuntabilitas pemesan).
            $table->foreignId('ordered_by')->nullable()->constrained('employees');
            $table->dateTime('ordered_at');
            $table->dateTime('scheduled_at')->nullable();
            $table->string('status')->default('ordered'); // ordered|scheduled|completed|cancelled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imaging_orders');
    }
};
