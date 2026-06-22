<?php

namespace Modules\GeneralTreatmentCategory\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralTreatmentCategory\Models\TreatmentCategory;
use Tests\TestCase;

class TreatmentCategoryControllerTest extends TestCase
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

    public function test_it_lists_treatment_categorie(): void
    {
        $this->actingUser();
        TreatmentCategory::factory()->count(3)->create();

        $this->getJson('/api/v1/treatment-categories')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_treatment_category(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/treatment-categories', ['name' => 'Contoh Kategoritindakan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Kategoritindakan');

        $this->assertDatabaseHas('treatment_categories', ['name' => 'Contoh Kategoritindakan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        TreatmentCategory::factory()->create(['name' => 'Contoh Kategoritindakan']);

        $this->postJson('/api/v1/treatment-categories', ['name' => 'Contoh Kategoritindakan'])->assertStatus(422);
    }

    public function test_it_deletes_treatment_category(): void
    {
        $this->actingUser();
        $treatmentCategory = TreatmentCategory::factory()->create();

        $this->deleteJson("/api/v1/treatment-categories/{$treatmentCategory->id}")->assertStatus(204);
        $this->assertDatabaseMissing('treatment_categories', ['id' => $treatmentCategory->id]);
    }
}