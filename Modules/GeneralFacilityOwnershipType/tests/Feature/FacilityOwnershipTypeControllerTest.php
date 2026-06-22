<?php

namespace Modules\GeneralFacilityOwnershipType\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralFacilityOwnershipType\Models\FacilityOwnershipType;
use Tests\TestCase;

class FacilityOwnershipTypeControllerTest extends TestCase
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

    public function test_it_lists_facility_ownership_type(): void
    {
        $this->actingUser();
        FacilityOwnershipType::factory()->count(3)->create();

        $this->getJson('/api/v1/facility-ownership-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_facility_ownership_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/facility-ownership-types', ['name' => 'Contoh Kepemilikantempatpelayanan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Kepemilikantempatpelayanan');

        $this->assertDatabaseHas('facility_ownership_types', ['name' => 'Contoh Kepemilikantempatpelayanan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        FacilityOwnershipType::factory()->create(['name' => 'Contoh Kepemilikantempatpelayanan']);

        $this->postJson('/api/v1/facility-ownership-types', ['name' => 'Contoh Kepemilikantempatpelayanan'])->assertStatus(422);
    }

    public function test_it_deletes_facility_ownership_type(): void
    {
        $this->actingUser();
        $facilityOwnershipType = FacilityOwnershipType::factory()->create();

        $this->deleteJson("/api/v1/facility-ownership-types/{$facilityOwnershipType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('facility_ownership_types', ['id' => $facilityOwnershipType->id]);
    }
}