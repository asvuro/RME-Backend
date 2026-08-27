<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananTelemedicineSession\Http\Controllers\TelemedicineSessionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // Param disingkat jadi {session} — 'telemedicine_session' masih lolos batas
    // 32 char, tapi konsisten dengan rute aksi di bawah yang memakai {session}.
    Route::apiResource('telemedicine-sessions', TelemedicineSessionController::class)
        ->only(['index', 'show'])
        ->parameters(['telemedicine-sessions' => 'session']);

    Route::apiResource('telemedicine-sessions', TelemedicineSessionController::class)
        ->only(['store', 'update', 'destroy'])
        ->parameters(['telemedicine-sessions' => 'session']);

    Route::post('telemedicine-sessions/{session}/start', [TelemedicineSessionController::class, 'start']);
    Route::post('telemedicine-sessions/{session}/complete', [TelemedicineSessionController::class, 'complete']);
});
