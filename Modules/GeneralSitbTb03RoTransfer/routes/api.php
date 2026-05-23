<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbTb03RoTransfer\Http\Controllers\SitbTb03RoTransferController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-tb03-ro-transfers', SitbTb03RoTransferController::class);
});