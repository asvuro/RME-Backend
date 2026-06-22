<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordBaepDepressionDetail\Http\Controllers\BaepDepressionDetailController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('baep-depression-details', BaepDepressionDetailController::class)->only(['index', 'show'])->parameters(['baep-depression-details' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('baep-depression-details', BaepDepressionDetailController::class)->only(['store'])->parameters(['baep-depression-details' => 'record']);
    });
});
