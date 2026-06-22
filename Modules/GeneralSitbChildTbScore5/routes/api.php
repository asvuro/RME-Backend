<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbChildTbScore5\Http\Controllers\SitbChildTbScore5Controller;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-child-tb-score5s', SitbChildTbScore5Controller::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('sitb-child-tb-score5s', SitbChildTbScore5Controller::class)->only(['store', 'update', 'destroy']);
    });
});
