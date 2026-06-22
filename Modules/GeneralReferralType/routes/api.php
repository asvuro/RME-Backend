<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralReferralType\Http\Controllers\ReferralTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('referral-types', ReferralTypeController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('referral-types', ReferralTypeController::class)->only(['store', 'update', 'destroy']);
    });
});
