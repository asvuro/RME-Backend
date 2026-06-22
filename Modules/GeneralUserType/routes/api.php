<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralUserType\Http\Controllers\UserTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('user-types', UserTypeController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('user-types', UserTypeController::class)->only(['store', 'update', 'destroy']);
    });
});
