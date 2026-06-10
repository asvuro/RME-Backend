<?php

use Illuminate\Support\Facades\Route;
use Modules\SystemLicenseGuard\Http\Controllers\LicenseController;

Route::prefix('v1/system/license')->group(function () {
    Route::get('/status', [LicenseController::class, 'status'])->middleware('throttle:60,1');
    Route::get('/fingerprint', [LicenseController::class, 'fingerprint'])->middleware('throttle:60,1');
    Route::post('/activate', [LicenseController::class, 'activate'])->middleware('throttle:10,1');
    Route::post('/sync', [LicenseController::class, 'sync'])->middleware('throttle:10,1');
    Route::post('/webhook', [LicenseController::class, 'webhook'])->middleware('throttle:30,1');
});
