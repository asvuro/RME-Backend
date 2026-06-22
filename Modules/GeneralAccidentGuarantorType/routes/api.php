<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralAccidentGuarantorType\Http\Controllers\AccidentGuarantorTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('accident-guarantor-types', AccidentGuarantorTypeController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('accident-guarantor-types', AccidentGuarantorTypeController::class)->only(['store', 'update', 'destroy']);
    });
});
