<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Port kolom pembayaran.penjamin_tagihan simgos2 yang belum ada:
     * KE → sequence (penjamin pertama = penanggung utama) dan
     * KELAS_KLAIM → room_class_id. Unique (invoice, guarantor) meniru dedup
     * storePenjaminTagihan ("bila belum ada").
     */
    public function up(): void
    {
        Schema::table('invoice_guarantors', function (Blueprint $table) {
            $table->unsignedInteger('sequence')->default(1)->after('covered_amount');
            $table->foreignId('room_class_id')->nullable()->after('sequence')
                ->constrained('room_classes')->nullOnDelete();
            $table->unique(['invoice_id', 'guarantor_id']);
        });
    }

    public function down(): void
    {
        Schema::table('invoice_guarantors', function (Blueprint $table) {
            $table->dropUnique(['invoice_id', 'guarantor_id']);
            $table->dropConstrainedForeignId('room_class_id');
            $table->dropColumn('sequence');
        });
    }
};
