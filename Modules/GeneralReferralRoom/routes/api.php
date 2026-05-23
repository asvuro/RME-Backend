<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralReferralRoom\Http\Controllers\ReferralRoomController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('referral-rooms', ReferralRoomController::class);
});