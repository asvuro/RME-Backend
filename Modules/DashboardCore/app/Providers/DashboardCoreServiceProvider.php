<?php

namespace Modules\DashboardCore\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class DashboardCoreServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'DashboardCore';

    protected string $nameLower = 'dashboardcore';

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        RouteServiceProvider::class,
    ];
}
