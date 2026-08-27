<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordImageMarker\Http\Controllers\ImageMarkerController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('image-markers', ImageMarkerController::class)->only(['index', 'show'])->parameters(['image-markers' => 'record']);

    Route::apiResource('image-markers', ImageMarkerController::class)->only(['store', 'update', 'destroy'])->parameters(['image-markers' => 'record']);
});
