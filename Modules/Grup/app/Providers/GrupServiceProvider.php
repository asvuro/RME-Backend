<?php

namespace Modules\Grup\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\DB;
use Modules\Grup\Console\Commands\ListenGroupRealtimeCommand;
use Modules\Grup\Console\Commands\SyncGroupMembershipCommand;
use Modules\Grup\Http\Middleware\VerifyGroupHubSignature;
use Nwidart\Modules\Support\ModuleServiceProvider;

class GrupServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Grup';

    protected string $nameLower = 'grup';

    protected array $providers = [RouteServiceProvider::class];

    public function register(): void
    {
        parent::register();
        $this->mergeConfigFrom(dirname(__DIR__, 2).'/config/grup.php', 'grup');
    }

    public function boot(): void
    {
        parent::boot();
        $this->loadMigrationsFrom(dirname(__DIR__, 2).'/database/migrations');
        $this->app->make(Router::class)->aliasMiddleware('grup.hub-signature', VerifyGroupHubSignature::class);

        if ($this->app->runningInConsole()) {
            $this->commands([SyncGroupMembershipCommand::class, ListenGroupRealtimeCommand::class]);
            $this->app->booted(function () {
                $this->app->make(Schedule::class)->command('grup:sync-membership')->everyFifteenMinutes()->withoutOverlapping();
                $this->app->make(Schedule::class)->call(
                    fn () => DB::table('grup_hub_nonces')->where('received_at', '<', now()->subDay())->delete(),
                )->daily()->name('grup:prune-nonces')->withoutOverlapping();
            });
        }
    }
}
