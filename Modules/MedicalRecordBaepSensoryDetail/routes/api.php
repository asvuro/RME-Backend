<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordBaepSensoryDetail\Http\Controllers\BaepSensoryDetailController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('baep-sensory-details', BaepSensoryDetailController::class)->only(['index', 'show'])->parameters(['baep-sensory-details' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('baep-sensory-details', BaepSensoryDetailController::class)->only(['store'])->parameters(['baep-sensory-details' => 'record']);
    });
});
