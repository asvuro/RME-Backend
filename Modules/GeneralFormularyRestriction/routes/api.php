<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralFormularyRestriction\Http\Controllers\FormularyRestrictionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('formulary-restrictions', FormularyRestrictionController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('formulary-restrictions', FormularyRestrictionController::class)->only(['store', 'update', 'destroy']);
    });
});
