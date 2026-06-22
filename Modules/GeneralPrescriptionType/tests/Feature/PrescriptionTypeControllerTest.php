<?php

namespace Modules\GeneralPrescriptionType\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralPrescriptionType\Models\PrescriptionType;
use Tests\TestCase;

class PrescriptionTypeControllerTest extends TestCase
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

    public function test_it_lists_prescription_type(): void
    {
        $this->actingUser();
        PrescriptionType::factory()->count(3)->create();

        $this->getJson('/api/v1/prescription-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_prescription_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/prescription-types', ['name' => 'Contoh Jenisresep', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenisresep');

        $this->assertDatabaseHas('prescription_types', ['name' => 'Contoh Jenisresep']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        PrescriptionType::factory()->create(['name' => 'Contoh Jenisresep']);

        $this->postJson('/api/v1/prescription-types', ['name' => 'Contoh Jenisresep'])->assertStatus(422);
    }

    public function test_it_deletes_prescription_type(): void
    {
        $this->actingUser();
        $prescriptionType = PrescriptionType::factory()->create();

        $this->deleteJson("/api/v1/prescription-types/{$prescriptionType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('prescription_types', ['id' => $prescriptionType->id]);
    }
}