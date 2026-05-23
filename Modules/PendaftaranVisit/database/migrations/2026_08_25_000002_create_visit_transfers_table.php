<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Riwayat gerbang mutasi ala pendaftaran.mutasi simgos2: satu baris per
// perpindahan bed/ward selama kunjungan berlangsung.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->foreignId('ward_from_id')->nullable()->constrained('wards')->nullOnDelete();
            $table->foreignId('bed_from_id')->nullable()->constrained('beds')->nullOnDelete();
            $table->foreignId('ward_to_id')->constrained('wards');
            $table->foreignId('bed_to_id')->nullable()->constrained('beds')->nullOnDelete();
            $table->foreignId('transferred_by')->constrained('users');
            $table->dateTime('transferred_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['visit_id', 'transferred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_transfers');
    }
};
