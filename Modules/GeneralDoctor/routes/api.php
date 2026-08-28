<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralDoctor\Http\Controllers\DoctorController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('doctors', DoctorController::class)->names('generaldoctor.doctors')->only(['index', 'show']);

    Route::apiResource('doctors', DoctorController::class)->names('generaldoctor.doctors')->only(['store', 'update', 'destroy']);
});
