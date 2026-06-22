<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananLabCultureResult\Http\Controllers\LabCultureResultController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('lab-culture-results', LabCultureResultController::class)->only(['index', 'show'])->parameters(['lab-culture-results' => 'culture_result']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('lab-culture-results', LabCultureResultController::class)->only(['store', 'update'])->parameters(['lab-culture-results' => 'culture_result']);
    });
});
