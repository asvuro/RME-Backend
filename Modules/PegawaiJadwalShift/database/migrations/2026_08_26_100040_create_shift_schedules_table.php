<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_schedules', function (Blueprint $table) {
            $table->id();
            // Salah satu dari staff_member_id / employee_id wajib diisi (gerbang
            // di ShiftScheduleService), tidak boleh keduanya sekaligus. Dua kolom
            // dipisah (bukan satu polymorphic) karena StaffMember dan Employee
            // adalah dua tabel master yang berbeda di modul lain.
            $table->foreignId('staff_member_id')->nullable()->constrained('staff_members')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('ward_id')->nullable()->constrained('wards')->nullOnDelete();
            $table->enum('shift_type', ['pagi', 'siang', 'malam']);
            $table->date('shift_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['scheduled', 'confirmed', 'absent'])->default('scheduled');
            $table->timestamps();

            // Cegah duplikat per pegawai + tanggal + jenis shift. MySQL menganggap
            // NULL tidak sama dengan NULL sehingga unique constraint ini tidak
            // saling menghalangi antara baris yang pakai staff_member_id dan yang
            // pakai employee_id — keduanya dijaga terpisah di sini, dan gerbang
            // "harus isi salah satu" ada di service, bukan di DB.
            $table->unique(['staff_member_id', 'shift_date', 'shift_type'], 'shift_schedules_staff_unique');
            $table->unique(['employee_id', 'shift_date', 'shift_type'], 'shift_schedules_employee_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_schedules');
    }
};
