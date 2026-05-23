<?php

namespace Modules\AuditActivityLog\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Modules\AuditActivityLog\Console\AuditPruneCommand;

class AuditActivityLogServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'AuditActivityLog';

    protected string $nameLower = 'auditactivitylog';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    protected array $commands = [
        AuditPruneCommand::class,
    ];

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
