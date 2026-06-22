<?php

namespace Modules\GeneralTbPatientCategory\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralTbPatientCategory\Models\TbPatientCategory;
use Tests\TestCase;

class TbPatientCategoryControllerTest extends TestCase
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

    public function test_it_lists_tb_patient_categorie(): void
    {
        $this->actingUser();
        TbPatientCategory::factory()->count(3)->create();

        $this->getJson('/api/v1/tb-patient-categories')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_tb_patient_category(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/tb-patient-categories', ['name' => 'Contoh Kategoripenderitatb', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Kategoripenderitatb');

        $this->assertDatabaseHas('tb_patient_categories', ['name' => 'Contoh Kategoripenderitatb']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        TbPatientCategory::factory()->create(['name' => 'Contoh Kategoripenderitatb']);

        $this->postJson('/api/v1/tb-patient-categories', ['name' => 'Contoh Kategoripenderitatb'])->assertStatus(422);
    }

    public function test_it_deletes_tb_patient_category(): void
    {
        $this->actingUser();
        $tbPatientCategory = TbPatientCategory::factory()->create();

        $this->deleteJson("/api/v1/tb-patient-categories/{$tbPatientCategory->id}")->assertStatus(204);
        $this->assertDatabaseMissing('tb_patient_categories', ['id' => $tbPatientCategory->id]);
    }
}