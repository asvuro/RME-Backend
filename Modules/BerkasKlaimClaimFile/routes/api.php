<?php

use Illuminate\Support\Facades\Route;
use Modules\BerkasKlaimClaimFile\Http\Controllers\BerkasKlaimClaimFileController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('claim-files', BerkasKlaimClaimFileController::class)->only(['index', 'show'])->parameters(['claim-files' => 'claim_file']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('claim-files', BerkasKlaimClaimFileController::class)->only(['store', 'update', 'destroy'])->parameters(['claim-files' => 'claim_file']);
    });
});
