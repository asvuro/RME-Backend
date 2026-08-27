<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordGynecologyHistory\Http\Controllers\GynecologyHistoryController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('gynecology-histories', GynecologyHistoryController::class)->only(['index', 'show'])->parameters(['gynecology-histories' => 'record']);

    Route::apiResource('gynecology-histories', GynecologyHistoryController::class)->only(['store'])->parameters(['gynecology-histories' => 'record']);
});
