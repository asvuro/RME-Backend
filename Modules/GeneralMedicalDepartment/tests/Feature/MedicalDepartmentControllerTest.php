<?php

namespace Modules\GeneralMedicalDepartment\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralMedicalDepartment\Models\MedicalDepartment;
use Tests\TestCase;

class MedicalDepartmentControllerTest extends TestCase
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

    public function test_it_lists_medical_departments(): void
    {
        $this->actingUser();
        MedicalDepartment::factory()->count(3)->create();

        $this->getJson('/api/v1/medical-departments')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_medical_department(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/medical-departments', ['name' => 'Internal Medicine', 'code' => 'IPD'])
            ->assertCreated()
            ->assertJsonPath('name', 'Internal Medicine');

        $this->assertDatabaseHas('medical_departments', ['name' => 'Internal Medicine']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        MedicalDepartment::factory()->create(['name' => 'Internal Medicine']);

        $this->postJson('/api/v1/medical-departments', ['name' => 'Internal Medicine'])->assertStatus(422);
    }

    public function test_it_deletes_medical_department(): void
    {
        $this->actingUser();
        $department = MedicalDepartment::factory()->create();

        $this->deleteJson("/api/v1/medical-departments/{$department->id}")->assertStatus(204);
        $this->assertDatabaseMissing('medical_departments', ['id' => $department->id]);
    }
}
