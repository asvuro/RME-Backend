<?php

namespace Modules\GeneralHealthProviderType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralHealthProviderType\Models\HealthProviderType;
use Tests\TestCase;

class HealthProviderTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_health_provider_type(): void
    {
        $this->actingUser();
        HealthProviderType::factory()->count(3)->create();

        $this->getJson('/api/v1/health-provider-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_health_provider_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/health-provider-types', ['name' => 'Contoh Jenisppk', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenisppk');

        $this->assertDatabaseHas('health_provider_types', ['name' => 'Contoh Jenisppk']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        HealthProviderType::factory()->create(['name' => 'Contoh Jenisppk']);

        $this->postJson('/api/v1/health-provider-types', ['name' => 'Contoh Jenisppk'])->assertStatus(422);
    }

    public function test_it_deletes_health_provider_type(): void
    {
        $this->actingUser();
        $healthProviderType = HealthProviderType::factory()->create();

        $this->deleteJson("/api/v1/health-provider-types/{$healthProviderType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('health_provider_types', ['id' => $healthProviderType->id]);
    }
}