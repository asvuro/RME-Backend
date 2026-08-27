<?php

use Illuminate\Support\Facades\Route;
use Modules\BerkasKlaimClaimCompleteness\Http\Controllers\BerkasKlaimClaimCompletenessController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('claim-completeness', BerkasKlaimClaimCompletenessController::class)->only(['index', 'show'])->parameters(['claim-completeness' => 'claim_completeness']);

    Route::apiResource('claim-completeness', BerkasKlaimClaimCompletenessController::class)->only(['store', 'update', 'destroy'])->parameters(['claim-completeness' => 'claim_completeness']);
});
