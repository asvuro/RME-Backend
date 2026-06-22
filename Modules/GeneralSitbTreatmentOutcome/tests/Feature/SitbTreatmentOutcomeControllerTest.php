<?php

namespace Modules\GeneralSitbTreatmentOutcome\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbTreatmentOutcome\Models\SitbTreatmentOutcome;
use Tests\TestCase;

class SitbTreatmentOutcomeControllerTest extends TestCase
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

    public function test_it_lists_sitb_treatment_outcome(): void
    {
        $this->actingUser();
        SitbTreatmentOutcome::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-treatment-outcomes')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_treatment_outcome(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-treatment-outcomes', ['name' => 'Contoh Sitbhasilakhirpengobatan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbhasilakhirpengobatan');

        $this->assertDatabaseHas('sitb_treatment_outcomes', ['name' => 'Contoh Sitbhasilakhirpengobatan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbTreatmentOutcome::factory()->create(['name' => 'Contoh Sitbhasilakhirpengobatan']);

        $this->postJson('/api/v1/sitb-treatment-outcomes', ['name' => 'Contoh Sitbhasilakhirpengobatan'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_treatment_outcome(): void
    {
        $this->actingUser();
        $sitbTreatmentOutcome = SitbTreatmentOutcome::factory()->create();

        $this->deleteJson("/api/v1/sitb-treatment-outcomes/{$sitbTreatmentOutcome->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_treatment_outcomes', ['id' => $sitbTreatmentOutcome->id]);
    }
}