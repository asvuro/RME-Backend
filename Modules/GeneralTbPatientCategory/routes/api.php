<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralTbPatientCategory\Http\Controllers\TbPatientCategoryController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('tb-patient-categories', TbPatientCategoryController::class)->only(['index', 'show']);

    Route::apiResource('tb-patient-categories', TbPatientCategoryController::class)->only(['store', 'update', 'destroy']);
});
