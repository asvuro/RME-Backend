<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralTreatmentCategory\Http\Controllers\TreatmentCategoryController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('treatment-categories', TreatmentCategoryController::class);
});