<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blood_bags', function (Blueprint $table) {
            $table->id();
            $table->string('bag_number')->unique();
            $table->foreignId('blood_type_id')->constrained('blood_types')->restrictOnDelete();
            $table->unsignedInteger('volume_ml');
            $table->dateTime('collected_at');
            $table->dateTime('expires_at');
            // in_stock -> crossmatch_reserved -> transfused (jalur normal);
            // in_stock -> discarded_expired / released juga valid. Lihat
            // Modules\InventoryBloodBag\Services\BloodBankService.
            $table->string('status')->default('in_stock');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blood_bags');
    }
};
