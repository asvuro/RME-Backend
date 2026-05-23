<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbChildTbScore0To13\Http\Controllers\SitbChildTbScore0To13Controller;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-child-tb-score0-to13s', SitbChildTbScore0To13Controller::class);
});