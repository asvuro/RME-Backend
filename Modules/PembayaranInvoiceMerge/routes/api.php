<?php

use Illuminate\Support\Facades\Route;
use Modules\PembayaranInvoiceMerge\Http\Controllers\InvoiceMergeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('invoice-merges', InvoiceMergeController::class)->only(['index', 'show'])->parameters(['invoice-merges' => 'invoice_merge']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('invoice-merges', InvoiceMergeController::class)->only(['store', 'update', 'destroy'])->parameters(['invoice-merges' => 'invoice_merge']);
    });
});
