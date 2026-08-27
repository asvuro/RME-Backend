<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralAntibioticBacteriaMapping\Http\Controllers\AntibioticBacteriaMappingController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('antibiotic-bacteria-mappings', AntibioticBacteriaMappingController::class)->only(['index', 'show']);

    Route::apiResource('antibiotic-bacteria-mappings', AntibioticBacteriaMappingController::class)->only(['store', 'update', 'destroy']);
});
