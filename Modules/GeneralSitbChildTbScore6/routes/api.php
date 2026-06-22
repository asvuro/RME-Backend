<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbChildTbScore6\Http\Controllers\SitbChildTbScore6Controller;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-child-tb-score6s', SitbChildTbScore6Controller::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('sitb-child-tb-score6s', SitbChildTbScore6Controller::class)->only(['store', 'update', 'destroy']);
    });
});
