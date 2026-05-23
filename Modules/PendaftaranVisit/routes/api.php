<?php

use Illuminate\Support\Facades\Route;
use Modules\PendaftaranVisit\Http\Controllers\VisitController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('visits', VisitController::class);
    Route::post('visits/{visit}/transfer', [VisitController::class, 'transfer']);
    Route::post('visits/{visit}/discharge', [VisitController::class, 'discharge']);
});
