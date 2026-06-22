<?php

namespace Modules\GeneralServiceTariffDistribution\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralServiceTariffDistribution\Models\ServiceTariffDistribution;
use Tests\TestCase;

class ServiceTariffDistributionControllerTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }
    private function actingUser(): void
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');
    }

    public function test_it_lists_items(): void
    {
        $this->actingUser();
        ServiceTariffDistribution::factory()->count(3)->create();

        $this->getJson('/api/v1/service-tariff-distributions')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_item(): void
    {
        $this->actingUser();
        $payload = ServiceTariffDistribution::factory()->make()->toArray();
        $this->postJson('/api/v1/service-tariff-distributions', $payload)->assertCreated();
    }

    public function test_it_updates_item(): void
    {
        $this->actingUser();
        $item = ServiceTariffDistribution::factory()->create();
        $payload = ServiceTariffDistribution::factory()->make()->toArray();
        $this->putJson("/api/v1/service-tariff-distributions/{$item->id}", $payload)->assertOk();
    }

    public function test_it_deletes_item(): void
    {
        $this->actingUser();
        $item = ServiceTariffDistribution::factory()->create();
        $this->deleteJson("/api/v1/service-tariff-distributions/{$item->id}")->assertStatus(204);
    }
}
