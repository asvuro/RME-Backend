<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // TTE internal: state machine draft -> pending_sign -> signed -> locked
        // atas referensi polymorphic (ref_type + ref_id, mis. resume medis).
        // Tidak ada panggilan API eksternal PSrE/BSrE -- itu future work.
        Schema::create('tte_documents', function (Blueprint $table) {
            $table->id();
            $table->string('ref_type', 30);
            $table->unsignedBigInteger('ref_id');
            $table->string('status', 20)->default('draft');
            $table->json('content')->nullable();
            $table->string('document_hash', 64)->nullable();
            $table->foreignId('signed_by')->nullable()->constrained('employees')->nullOnDelete();
            $table->dateTime('signed_at')->nullable();
            $table->timestamps();

            // Satu dokumen TTE aktif (belum locked) per referensi -- dijaga di
            // level service (unique index penuh akan menghalangi re-signing
            // setelah locked untuk versi dokumen baru, jadi tidak diberi unique
            // constraint DB di sini, cukup index untuk lookup cepat).
            $table->index(['ref_type', 'ref_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tte_documents');
    }
};
