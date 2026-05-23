<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralAccommodationCalculationRule\Http\Controllers\AccommodationCalculationRuleController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('accommodation-calculation-rules', AccommodationCalculationRuleController::class);
});