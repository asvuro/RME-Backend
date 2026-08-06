<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('linen_items', function (Blueprint $table) {
            $table->id();
            $table->string('linen_code')->unique();
            $table->string('linen_type');
            $table->foreignId('ward_id')->nullable()->constrained('wards')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('linen_items');
    }
};
