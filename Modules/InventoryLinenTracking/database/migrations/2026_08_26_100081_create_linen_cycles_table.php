<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('linen_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('linen_item_id')->constrained('linen_items')->cascadeOnDelete();
            $table->string('status')->default('dikirim_londri');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('linen_cycles');
    }
};
