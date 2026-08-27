<?php

use Illuminate\Support\Facades\Route;
use Modules\PembayaranRegistrationInvoice\Http\Controllers\RegistrationInvoiceController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('registration-invoices', RegistrationInvoiceController::class)->only(['index', 'show'])->parameters(['registration-invoices' => 'registration_invoice']);

    Route::apiResource('registration-invoices', RegistrationInvoiceController::class)->only(['store', 'update', 'destroy'])->parameters(['registration-invoices' => 'registration_invoice']);
});
