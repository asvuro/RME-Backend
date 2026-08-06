<?php

namespace Modules\FinanceGeneralLedger\Providers;

use App\Events\InvoiceLocked;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\FinanceGeneralLedger\Listeners\PostInvoiceLockedToLedger;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        InvoiceLocked::class => [PostInvoiceLockedToLedger::class],
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
