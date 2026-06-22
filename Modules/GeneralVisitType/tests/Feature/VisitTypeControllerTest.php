<?php

namespace Modules\GeneralVisitType\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralVisitType\Models\VisitType;
use Tests\TestCase;

class VisitTypeControllerTest extends TestCase
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

    public function test_it_lists_visit_type(): void
    {
        $this->actingUser();
        VisitType::factory()->count(3)->create();

        $this->getJson('/api/v1/visit-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_visit_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/visit-types', ['name' => 'Contoh Jeniskunjungan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jeniskunjungan');

        $this->assertDatabaseHas('visit_types', ['name' => 'Contoh Jeniskunjungan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        VisitType::factory()->create(['name' => 'Contoh Jeniskunjungan']);

        $this->postJson('/api/v1/visit-types', ['name' => 'Contoh Jeniskunjungan'])->assertStatus(422);
    }

    public function test_it_deletes_visit_type(): void
    {
        $this->actingUser();
        $visitType = VisitType::factory()->create();

        $this->deleteJson("/api/v1/visit-types/{$visitType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('visit_types', ['id' => $visitType->id]);
    }
}