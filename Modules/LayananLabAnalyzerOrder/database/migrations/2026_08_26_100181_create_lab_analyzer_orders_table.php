<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_analyzer_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            // Vendor boleh kosong: order bisa dicatat sebelum dipetakan ke analyzer.
            $table->foreignId('vendor_id')->nullable()->constrained('lab_analyzer_vendors')->nullOnDelete();
            $table->string('test_code');
            $table->foreignId('ordered_by')->constrained('employees')->cascadeOnDelete();
            $table->dateTime('ordered_at');
            // State machine: ordered -> sent_to_analyzer -> result_received -> verified.
            $table->string('status')->default('ordered')->index();
            // Hasil mentah apa adanya dari analyzer (tanpa parsing HL7/ASTM).
            $table->text('raw_result_text')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('employees')->nullOnDelete();
            $table->dateTime('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_analyzer_orders');
    }
};
