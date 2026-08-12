<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Kolom expiry untuk status 'reserved' (roadmap #11 follow-up): reservasi bed
// otomatis kedaluwarsa setelah TTL (default 60 menit, lihat bed.reservation_ttl_minutes)
// dan disapu oleh command bed:release-expired-reservations.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beds', function (Blueprint $table) {
            $table->dateTime('reserved_until')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('beds', function (Blueprint $table) {
            $table->dropColumn('reserved_until');
        });
    }
};
