<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananAntimicrobialStewardshipApproval\Http\Controllers\AntimicrobialStewardshipApprovalController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('antimicrobial-stewardship-approvals', AntimicrobialStewardshipApprovalController::class)->only(['index', 'show'])->parameters(['antimicrobial-stewardship-approvals' => 'amr_approval']);

    Route::apiResource('antimicrobial-stewardship-approvals', AntimicrobialStewardshipApprovalController::class)->only(['store'])->parameters(['antimicrobial-stewardship-approvals' => 'amr_approval']);
});
