<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralIdentityCardType\Http\Controllers\IdentityCardTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('identity-card-types', IdentityCardTypeController::class);
});