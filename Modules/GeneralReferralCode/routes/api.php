<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralReferralCode\Http\Controllers\ReferralCodeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('referral-codes', ReferralCodeController::class)->only(['index', 'show'])->parameters(['referral-codes' => 'referral_code']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('referral-codes', ReferralCodeController::class)->only(['store', 'update', 'destroy'])->parameters(['referral-codes' => 'referral_code']);
    });
});
