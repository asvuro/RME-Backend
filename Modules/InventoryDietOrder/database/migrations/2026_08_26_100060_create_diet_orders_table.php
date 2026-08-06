<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diet_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->string('diet_type');
            $table->unsignedInteger('calorie_target')->nullable();
            $table->text('allergy_notes')->nullable();
            $table->string('meal_schedule');
            $table->foreignId('ordered_by')->constrained('employees')->cascadeOnDelete();
            $table->string('status')->default('ordered');
            $table->date('order_date');
            $table->timestamps();

            $table->index(['order_date', 'meal_schedule']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diet_orders');
    }
};
