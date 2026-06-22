<?php

use Illuminate\Support\Facades\Route;
use Modules\PendaftaranFunction\Http\Controllers\PendaftaranFunctionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('registration-functions', PendaftaranFunctionController::class)->only(['index', 'show'])->parameters(['registration-functions' => 'registration_function']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('registration-functions', PendaftaranFunctionController::class)->only(['store', 'update', 'destroy'])->parameters(['registration-functions' => 'registration_function']);
    });
});
