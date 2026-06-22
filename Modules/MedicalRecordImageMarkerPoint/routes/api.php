<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordImageMarkerPoint\Http\Controllers\ImageMarkerPointController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('image-marker-points', ImageMarkerPointController::class)->only(['index', 'show'])->parameters(['image-marker-points' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('image-marker-points', ImageMarkerPointController::class)->only(['store', 'update', 'destroy'])->parameters(['image-marker-points' => 'record']);
    });
});
