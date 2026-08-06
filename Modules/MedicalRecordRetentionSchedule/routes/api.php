<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordRetentionSchedule\Http\Controllers\RetentionScheduleController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // Read-only dan dibatasi role:admin (bukan petugas|admin seperti modul
    // lain) — jadwal retensi menyangkut aturan pemusnahan berkas rekam
    // medis, bukan operasional harian.
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('retention-schedules', RetentionScheduleController::class)->only(['index', 'show']);
    });
});
