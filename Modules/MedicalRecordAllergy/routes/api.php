<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordAllergy\Http\Controllers\AllergyController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('allergies', AllergyController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('allergies', AllergyController::class)->only(['store', 'update']);
    });
});
