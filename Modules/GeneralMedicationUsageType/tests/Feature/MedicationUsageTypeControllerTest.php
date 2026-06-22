<?php

namespace Modules\GeneralMedicationUsageType\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralMedicationUsageType\Models\MedicationUsageType;
use Tests\TestCase;

class MedicationUsageTypeControllerTest extends TestCase
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

    public function test_it_lists_medication_usage_type(): void
    {
        $this->actingUser();
        MedicationUsageType::factory()->count(3)->create();

        $this->getJson('/api/v1/medication-usage-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_medication_usage_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/medication-usage-types', ['name' => 'Contoh Jenispenggunaanobat', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenispenggunaanobat');

        $this->assertDatabaseHas('medication_usage_types', ['name' => 'Contoh Jenispenggunaanobat']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        MedicationUsageType::factory()->create(['name' => 'Contoh Jenispenggunaanobat']);

        $this->postJson('/api/v1/medication-usage-types', ['name' => 'Contoh Jenispenggunaanobat'])->assertStatus(422);
    }

    public function test_it_deletes_medication_usage_type(): void
    {
        $this->actingUser();
        $medicationUsageType = MedicationUsageType::factory()->create();

        $this->deleteJson("/api/v1/medication-usage-types/{$medicationUsageType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('medication_usage_types', ['id' => $medicationUsageType->id]);
    }
}