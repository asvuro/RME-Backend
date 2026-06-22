<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralEmployeePhoto\Http\Controllers\GeneralEmployeePhotoController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('employee-photos', GeneralEmployeePhotoController::class)->only(['index', 'show'])->parameters(['employee-photos' => 'employeePhoto']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('employee-photos', GeneralEmployeePhotoController::class)->only(['store', 'update', 'destroy'])->parameters(['employee-photos' => 'employeePhoto']);
    });
});
