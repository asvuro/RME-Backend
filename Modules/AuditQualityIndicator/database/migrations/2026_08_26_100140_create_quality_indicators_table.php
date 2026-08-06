<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_indicators', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('unit_of_measure', 20)->default('%');
            // Nilai capaian yang diharapkan (mis. >= 95). Null = indikator
            // pemantauan tanpa ambang, hanya dicatat trennya.
            $table->decimal('target_value', 10, 2)->nullable();
            // klinis | manajerial | sasaran_keselamatan
            $table->string('category', 30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_indicators');
    }
};
