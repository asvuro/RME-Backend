<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralReferralStatus\Http\Controllers\ReferralStatusController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('referral-statuses', ReferralStatusController::class);
});