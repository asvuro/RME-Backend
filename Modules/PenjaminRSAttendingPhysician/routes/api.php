<?php

use Illuminate\Support\Facades\Route;
use Modules\PenjaminRSAttendingPhysician\Http\Controllers\PenjaminRSAttendingPhysicianController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('attending-physicians', PenjaminRSAttendingPhysicianController::class)->only(['index', 'show'])->parameters(['attending-physicians' => 'attending_physician']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('attending-physicians', PenjaminRSAttendingPhysicianController::class)->only(['store', 'update', 'destroy'])->parameters(['attending-physicians' => 'attending_physician']);
    });
});
