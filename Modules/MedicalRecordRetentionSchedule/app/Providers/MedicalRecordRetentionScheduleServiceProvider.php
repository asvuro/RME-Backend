<?php

namespace Modules\MedicalRecordRetentionSchedule\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Modules\MedicalRecordRetentionSchedule\Console\Commands\RetentionScanCommand;
use Nwidart\Modules\Support\ModuleServiceProvider;

class MedicalRecordRetentionScheduleServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'MedicalRecordRetentionSchedule';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'medicalrecordretentionschedule';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    protected array $commands = [
        RetentionScanCommand::class,
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
    protected function configureSchedules(Schedule $schedule): void
    {
        $schedule->command(RetentionScanCommand::class)->daily();
    }
}
