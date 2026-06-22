<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananExaminationResultStatus\Http\Controllers\ExaminationResultStatusController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('examination-result-statuses', ExaminationResultStatusController::class)->only(['index', 'show'])->parameters(['examination-result-statuses' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('examination-result-statuses', ExaminationResultStatusController::class)->only(['store', 'update', 'destroy'])->parameters(['examination-result-statuses' => 'record']);
    });
});
