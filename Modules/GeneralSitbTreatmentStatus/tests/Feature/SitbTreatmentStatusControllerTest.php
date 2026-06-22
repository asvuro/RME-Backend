<?php

namespace Modules\GeneralSitbTreatmentStatus\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbTreatmentStatus\Models\SitbTreatmentStatus;
use Tests\TestCase;

class SitbTreatmentStatusControllerTest extends TestCase
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

    public function test_it_lists_sitb_treatment_statuse(): void
    {
        $this->actingUser();
        SitbTreatmentStatus::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-treatment-statuses')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_treatment_status(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-treatment-statuses', ['name' => 'Contoh Sitbstatuspengobatan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbstatuspengobatan');

        $this->assertDatabaseHas('sitb_treatment_statuses', ['name' => 'Contoh Sitbstatuspengobatan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbTreatmentStatus::factory()->create(['name' => 'Contoh Sitbstatuspengobatan']);

        $this->postJson('/api/v1/sitb-treatment-statuses', ['name' => 'Contoh Sitbstatuspengobatan'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_treatment_status(): void
    {
        $this->actingUser();
        $sitbTreatmentStatus = SitbTreatmentStatus::factory()->create();

        $this->deleteJson("/api/v1/sitb-treatment-statuses/{$sitbTreatmentStatus->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_treatment_statuses', ['id' => $sitbTreatmentStatus->id]);
    }
}