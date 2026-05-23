<?php

namespace App\Providers;

use App\Modules\Support\CachedFileRepository;
use App\Modules\Support\CachedModuleManifest;
use Illuminate\Filesystem\Filesystem;
use Nwidart\Modules\Contracts\ActivatorInterface;
use Nwidart\Modules\Contracts\RepositoryInterface;
use Nwidart\Modules\LaravelModulesServiceProvider;
use Nwidart\Modules\ModuleManifest;

/**
 * Drop-in replacement for nwidart's LaravelModulesServiceProvider that serves
 * the module list from the warmed manifest (bootstrap/cache/module-manifest.php,
 * built by module:manifest-cache) instead of globbing every module.json under
 * Modules/ on every boot. Registered in bootstrap/providers.php because composer.json sets
 * dont-discover for nwidart/laravel-modules.
 */
class CachedLaravelModulesServiceProvider extends LaravelModulesServiceProvider
{
    /**
     * Register the service provider.
     *
     * The cached bindings are installed AFTER parent::register() on purpose:
     * parent::registerServices() unconditionally re-binds RepositoryInterface::class and
     * ModuleManifest::class with the stock closures (Laravel's singleton() overwrites any
     * existing binding), so binding first means our classes are silently replaced and never
     * used - which resurfaced as an I/O storm under APP_ENV=testing, where the stock
     * FileRepository/ModuleManifest deliberately bypass their own static caches via the
     * runningUnitTests() guards and re-glob all N module.json files on every lookup.
     * Binding last makes our cache-backed variants win. registerModules() inside
     * parent::register() still runs against the stock manifest once (a single glob, or a hit
     * on nwidart's own provider manifest); everything resolved afterwards - allEnabled(),
     * module_path(), find(), Migrator/translator callbacks - gets the cached classes. With
     * no warm manifest both classes fall back to a live scan, so a cold start still works.
     */
    public function register(): void
    {
        parent::register();

        $this->registerCachedServices();
    }

    /**
     * Swap the stock repository/manifest singletons for their cache-backed
     * variants. Same constructor shapes as the stock closures in
     * LaravelModulesServiceProvider::registerServices().
     */
    private function registerCachedServices(): void
    {
        $this->app->singleton(RepositoryInterface::class, function ($app) {
            return new CachedFileRepository($app, $app['config']->get('modules.paths.modules'));
        });
        $this->app->alias(RepositoryInterface::class, 'modules');

        $this->app->singleton(
            ModuleManifest::class,
            fn () => new CachedModuleManifest(
                new Filesystem,
                app(RepositoryInterface::class)->getScanPaths(),
                $this->getCachedModulePath(),
                app(ActivatorInterface::class)
            )
        );
    }
}
