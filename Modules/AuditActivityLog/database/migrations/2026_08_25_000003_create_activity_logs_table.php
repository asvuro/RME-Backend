<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Port logs.pengguna_akses_log simgos2 (AKSI C/R/U/D + OBJEK + REF +
// SEBELUM/SESUDAH). OBJEK char(5) diganti string bebas; partisi tahunan
// diganti command audit:prune.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 20);
            $table->string('object');
            $table->string('ref')->nullable();
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['object', 'ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
