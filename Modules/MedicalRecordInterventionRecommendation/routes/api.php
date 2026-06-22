<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordInterventionRecommendation\Http\Controllers\InterventionRecommendationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('intervention-recommendations', InterventionRecommendationController::class)->only(['index', 'show'])->parameters(['intervention-recommendations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('intervention-recommendations', InterventionRecommendationController::class)->only(['store', 'update', 'destroy'])->parameters(['intervention-recommendations' => 'record']);
    });
});
