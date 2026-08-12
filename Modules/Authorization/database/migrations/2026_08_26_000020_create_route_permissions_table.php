<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Metadata generator RBAC dinamis: satu baris per rute terdaftar, memetakan
// Controller@method -> nama Permission spatie + tier akses lama (role:...)
// yang dipakai sebagai baseline grant admin/petugas saat cutover. Tabel
// terpisah dari skema spatie bawaan (permissions/roles/dst) supaya tidak
// menyentuh asumsi skema milik package.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('route_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_id')->nullable()->constrained('permissions')->nullOnDelete();
            $table->string('method');
            $table->string('uri');
            $table->string('controller_action')->unique();
            $table->string('module');
            $table->string('legacy_tier'); // admin_only | petugas_admin | authenticated_any | public
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_permissions');
    }
};
