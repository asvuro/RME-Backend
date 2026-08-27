<?php

use Illuminate\Support\Facades\Route;
use Modules\AuditInfectionSurveillance\Http\Controllers\DeviceDayController;
use Modules\AuditInfectionSurveillance\Http\Controllers\InfectionCaseController;
use Modules\AuditInfectionSurveillance\Http\Controllers\SurveillanceRateController;

// Nama parameter apiResource ({device_day} = 10 char, {infection_case} = 14 char)
// masih di bawah batas 32 karakter Symfony — tidak perlu ->parameters().

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // Baca-saja: kalkulasi angka surveilans.
    Route::get('infection-surveillance/rate', [SurveillanceRateController::class, 'rate']);

    Route::apiResource('device-days', DeviceDayController::class)->only(['index', 'show']);
    Route::apiResource('infection-cases', InfectionCaseController::class)->only(['index', 'show']);

    Route::apiResource('device-days', DeviceDayController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('infection-cases', InfectionCaseController::class)->only(['store', 'update', 'destroy']);
});
