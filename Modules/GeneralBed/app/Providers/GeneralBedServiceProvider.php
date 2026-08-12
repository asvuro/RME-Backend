<?php

namespace Modules\GeneralBed\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Modules\GeneralBed\Console\Commands\ReleaseExpiredBedReservationsCommand;

class GeneralBedServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'GeneralBed';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'generalbed';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    protected array $commands = [
        ReleaseExpiredBedReservationsCommand::class,
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
     * TTL reservasi default 60 menit (bed.reservation_ttl_minutes) -- sapuan
     * tiap 5 menit supaya bed yang kedaluwarsa cepat kembali tersedia.
     *
     * @param $schedule
     */
    protected function configureSchedules(Schedule $schedule): void
    {
        $schedule->command(ReleaseExpiredBedReservationsCommand::class)->everyFiveMinutes();
    }
}
