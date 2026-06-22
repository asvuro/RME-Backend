<?php

use Illuminate\Support\Facades\Route;
use Modules\BerkasKlaimClaimCompletenessComment\Http\Controllers\BerkasKlaimClaimCompletenessCommentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('claim-completeness-comments', BerkasKlaimClaimCompletenessCommentController::class)->only(['index', 'show'])->parameters(['claim-completeness-comments' => 'claim_completeness_comment']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('claim-completeness-comments', BerkasKlaimClaimCompletenessCommentController::class)->only(['store', 'update', 'destroy'])->parameters(['claim-completeness-comments' => 'claim_completeness_comment']);
    });
});
