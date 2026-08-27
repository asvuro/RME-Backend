<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralDoctor\Http\Controllers\DoctorController;

Route::apiResource('doctors', DoctorController::class)->names('generaldoctor.doctors')->only(['index', 'show'])->middleware('auth:sanctum');

Route::apiResource('doctors', DoctorController::class)->names('generaldoctor.doctors')->only(['store', 'update', 'destroy'])->middleware(['auth:sanctum']);
