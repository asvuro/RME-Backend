<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananEarlyWarningScore\Http\Controllers\VitalSignObservationController;

// Param rute "vital_sign_observation" = 22 karakter, masih di bawah batas 32
// karakter parameter rute Symfony — tidak perlu ->parameters().
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('vital-sign-observations', VitalSignObservationController::class)
        ->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('vital-sign-observations', VitalSignObservationController::class)
            ->only(['store']);
    });
});
