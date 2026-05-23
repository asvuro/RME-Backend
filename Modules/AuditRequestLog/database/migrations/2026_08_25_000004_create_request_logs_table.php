<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Port semangat logs.bridge_log simgos2 (URL + payload + IP) untuk request
// API masuk: siapa memanggil apa, kapan, dengan status berapa.
// Response body TIDAK disimpan (volume & privasi).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_logs', function (Blueprint $table) {
            $table->id();
            $table->string('method', 10);
            $table->string('url');
            $table->unsignedInteger('status')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip', 45)->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_logs');
    }
};
