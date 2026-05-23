<?php

namespace Modules\AuditRequestLog\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class AuditRequestLogServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'AuditRequestLog';

    protected string $nameLower = 'auditrequestlog';

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
