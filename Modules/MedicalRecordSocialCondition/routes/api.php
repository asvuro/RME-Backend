<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordSocialCondition\Http\Controllers\SocialConditionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('social-conditions', SocialConditionController::class)->only(['index', 'show'])->parameters(['social-conditions' => 'record']);

    Route::apiResource('social-conditions', SocialConditionController::class)->only(['store', 'update', 'destroy'])->parameters(['social-conditions' => 'record']);
});
