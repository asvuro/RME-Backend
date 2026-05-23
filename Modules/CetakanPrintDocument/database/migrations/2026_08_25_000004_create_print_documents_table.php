<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Padan cetakan.karcis_pasien + cetakan.kwitansi_pembayaran simgos2:
        // setiap penerbitan dokumen tercatat dengan nomor seri, idempoten
        // per (jenis dokumen, referensi).
        Schema::create('print_documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_type', 30);
            $table->string('ref_type', 30);
            $table->unsignedBigInteger('ref_id');
            $table->string('document_number')->unique();
            $table->json('payload')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('issued_at');
            $table->timestamps();

            $table->unique(['document_type', 'ref_type', 'ref_id'], 'print_documents_issue_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_documents');
    }
};
