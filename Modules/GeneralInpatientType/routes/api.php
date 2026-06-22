<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralInpatientType\Http\Controllers\InpatientTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('inpatient-types', InpatientTypeController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('inpatient-types', InpatientTypeController::class)->only(['store', 'update', 'destroy']);
    });
});
