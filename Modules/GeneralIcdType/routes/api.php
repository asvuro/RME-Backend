<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralIcdType\Http\Controllers\IcdTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('icd-types', IcdTypeController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('icd-types', IcdTypeController::class)->only(['store', 'update', 'destroy']);
    });
});
