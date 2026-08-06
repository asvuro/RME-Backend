<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('remuneration_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();

            // Polymorphic-lite: menunjuk ke sumber tindakan/invoice_item lintas
            // modul (mis. tindakan, invoice_item) tanpa FK relasional supaya
            // modul ini tidak perlu tahu tabel spesifik sumbernya.
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');

            // operator_utama | asisten | anestesi, dst — peran dokter/pegawai
            // pada tindakan tsb, menentukan porsi remunerasi.
            $table->string('role');

            $table->decimal('gross_amount', 15, 2);
            $table->decimal('deduction_percentage', 5, 2)->default(0);
            $table->decimal('fixed_deduction', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2);

            // Tanggal tindakan/perolehan jasa medis — dipakai utk filter
            // summary per bulan/tahun (bukan created_at, supaya entri yang
            // diinput belakangan tetap dihitung pada periode tindakannya).
            $table->date('service_date');

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['source_type', 'source_id']);
            $table->index(['employee_id', 'service_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('remuneration_entries');
    }
};
