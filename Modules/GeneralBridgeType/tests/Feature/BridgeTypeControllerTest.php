<?php

namespace Modules\GeneralBridgeType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralBridgeType\Models\BridgeType;
use Tests\TestCase;

class BridgeTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_bridge_type(): void
    {
        $this->actingUser();
        BridgeType::factory()->count(3)->create();

        $this->getJson('/api/v1/bridge-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_bridge_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/bridge-types', ['name' => 'Contoh Jenisbridge', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenisbridge');

        $this->assertDatabaseHas('bridge_types', ['name' => 'Contoh Jenisbridge']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        BridgeType::factory()->create(['name' => 'Contoh Jenisbridge']);

        $this->postJson('/api/v1/bridge-types', ['name' => 'Contoh Jenisbridge'])->assertStatus(422);
    }

    public function test_it_deletes_bridge_type(): void
    {
        $this->actingUser();
        $bridgeType = BridgeType::factory()->create();

        $this->deleteJson("/api/v1/bridge-types/{$bridgeType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('bridge_types', ['id' => $bridgeType->id]);
    }
}