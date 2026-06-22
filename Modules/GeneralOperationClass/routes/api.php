<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralOperationClass\Http\Controllers\OperationClassController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('operation-classes', OperationClassController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('operation-classes', OperationClassController::class)->only(['store', 'update', 'destroy']);
    });
});
