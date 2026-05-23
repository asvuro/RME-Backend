<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sitb_child_tb_score6s', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code', 10)->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sitb_child_tb_score6s');
    }
};