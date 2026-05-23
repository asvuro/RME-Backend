<?php

namespace Modules\GeneralAnesthesiaType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralAnesthesiaType\Models\AnesthesiaType;
use Tests\TestCase;

class AnesthesiaTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_anesthesia_type(): void
    {
        $this->actingUser();
        AnesthesiaType::factory()->count(3)->create();

        $this->getJson('/api/v1/anesthesia-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_anesthesia_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/anesthesia-types', ['name' => 'Contoh Jenisanastesi', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenisanastesi');

        $this->assertDatabaseHas('anesthesia_types', ['name' => 'Contoh Jenisanastesi']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        AnesthesiaType::factory()->create(['name' => 'Contoh Jenisanastesi']);

        $this->postJson('/api/v1/anesthesia-types', ['name' => 'Contoh Jenisanastesi'])->assertStatus(422);
    }

    public function test_it_deletes_anesthesia_type(): void
    {
        $this->actingUser();
        $anesthesiaType = AnesthesiaType::factory()->create();

        $this->deleteJson("/api/v1/anesthesia-types/{$anesthesiaType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('anesthesia_types', ['id' => $anesthesiaType->id]);
    }
}