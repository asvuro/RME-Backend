<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drug_interaction_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id_a')->constrained('items')->cascadeOnDelete();
            $table->foreignId('item_id_b')->constrained('items')->cascadeOnDelete();
            // minor | moderate | major_contraindicated (divalidasi di service/FormRequest).
            $table->string('severity');
            $table->text('clinical_note');
            $table->timestamps();

            // Pasangan A-B unik pada urutan ini; pasangan terbalik B-A dicek di
            // gerbang service (DB constraint tidak bisa menyatakan "unordered pair").
            $table->unique(['item_id_a', 'item_id_b']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drug_interaction_rules');
    }
};
