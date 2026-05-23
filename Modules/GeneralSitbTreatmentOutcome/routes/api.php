<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbTreatmentOutcome\Http\Controllers\SitbTreatmentOutcomeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-treatment-outcomes', SitbTreatmentOutcomeController::class);
});