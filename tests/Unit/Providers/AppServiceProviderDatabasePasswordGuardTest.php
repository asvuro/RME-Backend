<?php

namespace Tests\Unit\Providers;

use App\Providers\AppServiceProvider;
use ReflectionMethod;
use Tests\TestCase;

/**
 * Production-readiness: fail fast di luar local/testing kalau DB_PASSWORD
 * kosong untuk koneksi non-sqlite (AppServiceProvider::boot()).
 */
class AppServiceProviderDatabasePasswordGuardTest extends TestCase
{
    protected function invokeGuard(): void
    {
        $provider = new AppServiceProvider($this->app);
        $method = new ReflectionMethod($provider, 'assertDatabasePasswordConfigured');
        $method->setAccessible(true);
        $method->invoke($provider);
    }

    public function test_throws_di_production_saat_password_kosong(): void
    {
        $this->app->detectEnvironment(fn () => 'production');
        config(['database.default' => 'mysql', 'database.connections.mysql.password' => '']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("DB_PASSWORD kosong");

        $this->invokeGuard();
    }

    public function test_lolos_di_production_saat_password_terisi(): void
    {
        $this->app->detectEnvironment(fn () => 'production');
        config(['database.default' => 'mysql', 'database.connections.mysql.password' => 'rahasia']);

        $this->invokeGuard();
        $this->addToAssertionCount(1);
    }

    public function test_lolos_di_production_untuk_koneksi_sqlite(): void
    {
        $this->app->detectEnvironment(fn () => 'production');
        config(['database.default' => 'sqlite']);

        $this->invokeGuard();
        $this->addToAssertionCount(1);
    }

    public function test_lolos_di_testing_walau_password_kosong(): void
    {
        $this->app->detectEnvironment(fn () => 'testing');
        config(['database.default' => 'mysql', 'database.connections.mysql.password' => '']);

        $this->invokeGuard();
        $this->addToAssertionCount(1);
    }
}
