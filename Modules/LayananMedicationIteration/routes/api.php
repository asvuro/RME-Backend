<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananMedicationIteration\Http\Controllers\MedicationIterationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('medication-iterations', MedicationIterationController::class)->only(['index', 'show'])->parameters(['medication-iterations' => 'iteration']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('medication-iterations', MedicationIterationController::class)->only(['store', 'update'])->parameters(['medication-iterations' => 'iteration']);
    });
});
