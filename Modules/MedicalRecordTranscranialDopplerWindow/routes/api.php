<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordTranscranialDopplerWindow\Http\Controllers\TranscranialDopplerWindowController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('tcd-windows', TranscranialDopplerWindowController::class)->only(['index', 'show'])->parameters(['tcd-windows' => 'record']);

    Route::apiResource('tcd-windows', TranscranialDopplerWindowController::class)->only(['store', 'update', 'destroy'])->parameters(['tcd-windows' => 'record']);
});
