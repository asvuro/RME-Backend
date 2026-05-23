<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPathologyExaminationType\Http\Controllers\PathologyExaminationTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pathology-examination-types', PathologyExaminationTypeController::class);
});