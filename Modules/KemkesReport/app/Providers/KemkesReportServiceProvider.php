<?php

namespace Modules\KemkesReport\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class KemkesReportServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'KemkesReport';

    protected string $nameLower = 'kemkesreport';

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        RouteServiceProvider::class,
    ];
}
