<?php

namespace App\Providers;

use App\Modules\Contracts\BedGate;
use App\Modules\Contracts\BillingGate;
use App\Modules\Contracts\HospitalConfig;
use App\Modules\Contracts\StockGate;
use App\Modules\Contracts\VisitGate;
use App\Modules\Contracts\WardScope;
use App\Support\RsSettingService;
use App\Support\WardAccessResolver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Kontrak antar-modul (#7 service layer): modul klinis bergantung pada
     * interface di app/Modules/Contracts, bukan model/service modul lain.
     */
    protected array $contracts = [
        HospitalConfig::class => RsSettingService::class,
        BillingGate::class => \Modules\PembayaranInvoice\Services\InvoiceService::class,
        VisitGate::class => \Modules\PendaftaranVisit\Services\VisitService::class,
        StockGate::class => \Modules\InventoryWardStockTransaction\Services\WardStockService::class,
        BedGate::class => \Modules\GeneralBed\Services\BedService::class,
        WardScope::class => WardAccessResolver::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        foreach ($this->contracts as $contract => $implementation) {
            $this->app->bind($contract, $implementation);
        }
        $this->app->singleton(RsSettingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
