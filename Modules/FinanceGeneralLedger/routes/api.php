<?php

use Illuminate\Support\Facades\Route;
use Modules\FinanceGeneralLedger\Http\Controllers\AccountController;
use Modules\FinanceGeneralLedger\Http\Controllers\JournalEntryController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('accounts', AccountController::class)->only(['index', 'show']);
        Route::apiResource('journal-entries', JournalEntryController::class)->only(['index', 'show']);
    });
});
