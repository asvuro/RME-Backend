<?php

namespace Modules\GeneralInpatientType\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralInpatientType\Models\InpatientType;
use Tests\TestCase;

class InpatientTypeControllerTest extends TestCase
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

    public function test_it_lists_inpatient_type(): void
    {
        $this->actingUser();
        InpatientType::factory()->count(3)->create();

        $this->getJson('/api/v1/inpatient-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_inpatient_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/inpatient-types', ['name' => 'Contoh Jenisrawatinap', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenisrawatinap');

        $this->assertDatabaseHas('inpatient_types', ['name' => 'Contoh Jenisrawatinap']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        InpatientType::factory()->create(['name' => 'Contoh Jenisrawatinap']);

        $this->postJson('/api/v1/inpatient-types', ['name' => 'Contoh Jenisrawatinap'])->assertStatus(422);
    }

    public function test_it_deletes_inpatient_type(): void
    {
        $this->actingUser();
        $inpatientType = InpatientType::factory()->create();

        $this->deleteJson("/api/v1/inpatient-types/{$inpatientType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('inpatient_types', ['id' => $inpatientType->id]);
    }
}