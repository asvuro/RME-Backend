<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralInvoiceType\Http\Controllers\InvoiceTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('invoice-types', InvoiceTypeController::class)->only(['index', 'show']);

    Route::apiResource('invoice-types', InvoiceTypeController::class)->only(['store', 'update', 'destroy']);
});
