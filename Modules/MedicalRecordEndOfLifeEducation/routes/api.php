<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordEndOfLifeEducation\Http\Controllers\EndOfLifeEducationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('end-of-life-educations', EndOfLifeEducationController::class)->only(['index', 'show'])->parameters(['end-of-life-educations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('end-of-life-educations', EndOfLifeEducationController::class)->only(['store', 'update', 'destroy'])->parameters(['end-of-life-educations' => 'record']);
    });
});
