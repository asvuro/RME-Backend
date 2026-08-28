<?php

namespace Modules\Grup\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function map(): void
    {
        Route::middleware('api')->prefix('api')->name('api.')->group(dirname(__DIR__, 2).'/routes/api.php');
    }
}
