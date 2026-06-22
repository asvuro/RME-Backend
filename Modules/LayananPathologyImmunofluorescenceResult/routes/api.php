<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananPathologyImmunofluorescenceResult\Http\Controllers\PathologyImmunofluorescenceResultController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pathology-immunofluorescence-results', PathologyImmunofluorescenceResultController::class)->only(['index', 'show'])->parameters(['pathology-immunofluorescence-results' => 'pa_if_result']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('pathology-immunofluorescence-results', PathologyImmunofluorescenceResultController::class)->only(['store'])->parameters(['pathology-immunofluorescence-results' => 'pa_if_result']);
    });
});
