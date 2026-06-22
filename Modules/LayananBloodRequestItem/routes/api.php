<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananBloodRequestItem\Http\Controllers\BloodRequestItemController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('blood-request-items', BloodRequestItemController::class)->only(['index', 'show'])->parameters(['blood-request-items' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('blood-request-items', BloodRequestItemController::class)->only(['store', 'update', 'destroy'])->parameters(['blood-request-items' => 'record']);
    });
});
