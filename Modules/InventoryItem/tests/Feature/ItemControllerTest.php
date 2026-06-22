<?php

namespace Modules\InventoryItem\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\InventoryItem\Models\Item;
use Tests\TestCase;

class ItemControllerTest extends TestCase
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

    public function test_it_creates_item(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/items', ['name' => 'Paracetamol 500mg', 'unit' => 'tablet'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Paracetamol 500mg');
    }

    public function test_it_increases_stock(): void
    {
        $this->actingUser();
        $item = Item::factory()->create(['stock_quantity' => 10]);

        $this->postJson("/api/v1/items/{$item->id}/adjust-stock", ['quantity' => 50])
            ->assertOk()
            ->assertJsonPath('data.stock_quantity', 60);
    }

    public function test_it_decreases_stock(): void
    {
        $this->actingUser();
        $item = Item::factory()->create(['stock_quantity' => 10]);

        $this->postJson("/api/v1/items/{$item->id}/adjust-stock", ['quantity' => -3])
            ->assertOk()
            ->assertJsonPath('data.stock_quantity', 7);
    }

    public function test_it_rejects_adjustment_that_would_go_negative(): void
    {
        $this->actingUser();
        $item = Item::factory()->create(['stock_quantity' => 5]);

        $this->postJson("/api/v1/items/{$item->id}/adjust-stock", ['quantity' => -10])
            ->assertStatus(422);

        $this->assertEquals(5, $item->fresh()->stock_quantity);
    }

    public function test_stock_quantity_is_not_editable_via_general_update(): void
    {
        $this->actingUser();
        $item = Item::factory()->create(['stock_quantity' => 10]);

        $this->putJson("/api/v1/items/{$item->id}", ['stock_quantity' => 999, 'name' => 'Renamed']);

        $this->assertEquals(10, $item->fresh()->stock_quantity);
        $this->assertEquals('Renamed', $item->fresh()->name);
    }
}
