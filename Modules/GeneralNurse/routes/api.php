<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralNurse\Http\Controllers\NurseController;

Route::apiResource('nurses', NurseController::class)->names('generalnurse.nurses')->only(['index', 'show'])->middleware('auth:sanctum');

Route::apiResource('nurses', NurseController::class)->names('generalnurse.nurses')->only(['store', 'update', 'destroy'])->middleware(['auth:sanctum', 'role:petugas|admin']);
