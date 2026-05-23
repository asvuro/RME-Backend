<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralAbsenceType\Http\Controllers\AbsenceTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('absence-types', AbsenceTypeController::class);
});