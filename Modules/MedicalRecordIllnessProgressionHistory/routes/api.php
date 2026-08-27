<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordIllnessProgressionHistory\Http\Controllers\IllnessProgressionHistoryController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('illness-progression-histories', IllnessProgressionHistoryController::class)->only(['index', 'show'])->parameters(['illness-progression-histories' => 'record']);

    Route::apiResource('illness-progression-histories', IllnessProgressionHistoryController::class)->only(['store'])->parameters(['illness-progression-histories' => 'record']);
});
