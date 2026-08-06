<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_indicator_records', function (Blueprint $table) {
            $table->id();
            // restrictOnDelete: indikator master tidak boleh hilang begitu ada
            // catatan capaian — riwayat INM adalah bukti mutu/akreditasi.
            $table->foreignId('indicator_id')->constrained('quality_indicators')->restrictOnDelete()->cascadeOnUpdate();
            $table->unsignedTinyInteger('period_month'); // 1..12
            $table->unsignedSmallInteger('period_year');
            $table->decimal('numerator', 14, 2)->default(0);
            $table->decimal('denominator', 14, 2)->default(0);
            // achieved_value TIDAK disimpan: dihitung dari numerator/denominator*100
            // lewat accessor QualityIndicatorRecord::achieved_value agar rumus
            // punya satu sumber kebenaran di kode, bukan angka beku di DB.
            $table->foreignId('recorded_by')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();

            // Satu baris capaian per indikator per bulan; duplikat periode
            // membuat tren ganda dan rata-rata tahunan menyesatkan.
            $table->unique(['indicator_id', 'period_month', 'period_year'], 'quality_indicator_records_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_indicator_records');
    }
};
