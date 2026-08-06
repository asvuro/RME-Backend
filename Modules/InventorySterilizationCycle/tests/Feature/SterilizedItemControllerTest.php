<?php

namespace Modules\InventorySterilizationCycle\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\InventorySterilizationCycle\Models\SterilizationCycle;
use Modules\InventorySterilizationCycle\Models\SterilizedItem;
use Tests\TestCase;

class SterilizedItemControllerTest extends TestCase
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

    public function test_it_lists_sterilized_items_filtered_by_cycle(): void
    {
        $this->actingUser();
        $cycleA = SterilizationCycle::factory()->passed()->create();
        $cycleB = SterilizationCycle::factory()->passed()->create();
        SterilizedItem::factory()->count(2)->create(['cycle_id' => $cycleA->id]);
        SterilizedItem::factory()->create(['cycle_id' => $cycleB->id]);

        $response = $this->getJson("/api/v1/sterilized-items?cycle_id={$cycleA->id}");

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }
}
