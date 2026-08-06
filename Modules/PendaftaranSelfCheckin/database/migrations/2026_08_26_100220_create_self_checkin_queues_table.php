<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('self_checkin_queues', function (Blueprint $table) {
            $table->id();
            // Pasien boleh null: tamu kiosk yang belum punya rekam medis hanya
            // meninggalkan NIK; pencocokan ke patients terjadi di petugas loket.
            $table->foreignId('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            // NIK hasil input kiosk (pasien baru / pencarian manual), denormalized.
            $table->string('nik', 16)->nullable()->index();
            // Nomor urut harian per poli (ward), format %03d, di-generate service.
            $table->string('queue_number', 10);
            $table->foreignId('ward_id')->nullable()->constrained('wards')->nullOnDelete();
            // Tanggal antrian (bagian tanggal dari checked_in_at). Kolom terpisah
            // supaya penomoran per-hari dan unique constraint berikut bisa
            // ditegakkan DB tanpa whereDate() per query.
            $table->date('queue_date')->index();
            $table->dateTime('checked_in_at');
            // waiting | called | completed | no_show
            // (no_show belum punya endpoint; disiapkan utk penandaan manual petugas.)
            $table->string('status')->default('waiting')->index();
            $table->dateTime('called_at')->nullable();
            // Petugas/user loket yang memanggil (aktor device/service account
            // dicatat lewat kolom ini saat aksi call).
            $table->foreignId('called_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Nomor unik per hari per poli. Catatan: baris dengan ward_id NULL
            // tidak ikut ditegakkan unique oleh MySQL — bucket "umum" kiosk
            // mengandalkan generator service sebagai pengaman utama.
            $table->unique(['ward_id', 'queue_date', 'queue_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('self_checkin_queues');
    }
};
