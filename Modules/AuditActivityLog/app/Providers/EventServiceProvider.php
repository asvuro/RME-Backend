<?php

namespace Modules\AuditActivityLog\Providers;

use App\Events\InvoiceLocked;
use App\Events\PrescriptionDispensed;
use App\Events\VisitAdmitted;
use App\Events\VisitDischarged;
use App\Events\VisitTransferred;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\AuditActivityLog\Listeners\DomainEventAuditListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Milestone domain #7–#11 → jejak audit semantik.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        VisitAdmitted::class => [DomainEventAuditListener::class],
        VisitTransferred::class => [DomainEventAuditListener::class],
        VisitDischarged::class => [DomainEventAuditListener::class],
        InvoiceLocked::class => [DomainEventAuditListener::class],
        PrescriptionDispensed::class => [DomainEventAuditListener::class],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = false;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}
