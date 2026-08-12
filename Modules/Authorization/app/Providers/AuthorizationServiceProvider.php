<?php

namespace Modules\Authorization\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Modules\Authorization\Console\Commands\SyncRoutePermissionsCommand;

class AuthorizationServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Authorization';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'authorization';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    protected array $commands = [
        SyncRoutePermissionsCommand::class,
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

    /**
     * Define module schedules.
     * 
     * @param $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    // }
}
