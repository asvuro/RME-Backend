<?php

namespace Modules\GeneralManufacturer\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralManufacturer\Models\Manufacturer;
use Tests\TestCase;

class ManufacturerControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_manufacturer(): void
    {
        $this->actingUser();
        Manufacturer::factory()->count(3)->create();

        $this->getJson('/api/v1/manufacturers')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_manufacturer(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/manufacturers', ['name' => 'Contoh Merkprodusen', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Merkprodusen');

        $this->assertDatabaseHas('manufacturers', ['name' => 'Contoh Merkprodusen']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        Manufacturer::factory()->create(['name' => 'Contoh Merkprodusen']);

        $this->postJson('/api/v1/manufacturers', ['name' => 'Contoh Merkprodusen'])->assertStatus(422);
    }

    public function test_it_deletes_manufacturer(): void
    {
        $this->actingUser();
        $manufacturer = Manufacturer::factory()->create();

        $this->deleteJson("/api/v1/manufacturers/{$manufacturer->id}")->assertStatus(204);
        $this->assertDatabaseMissing('manufacturers', ['id' => $manufacturer->id]);
    }
}