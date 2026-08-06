<?php

use Illuminate\Support\Facades\Route;
use Modules\PegawaiRemunerasiJasaMedis\Http\Controllers\RemunerationEntryController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // Didaftarkan sebelum apiResource show, supaya "summary" tidak tertangkap
    // sebagai parameter {remuneration_entry}.
    Route::get('remuneration-entries/summary', [RemunerationEntryController::class, 'summary']);

    Route::apiResource('remuneration-entries', RemunerationEntryController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('remuneration-entries', RemunerationEntryController::class)->only(['store', 'update', 'destroy']);
    });
});
