<?php

namespace Modules\GeneralSitbTreatmentHistoryClassification\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbTreatmentHistoryClassification\Models\SitbTreatmentHistoryClassification;
use Tests\TestCase;

class SitbTreatmentHistoryClassificationControllerTest extends TestCase
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

    public function test_it_lists_sitb_treatment_history_classification(): void
    {
        $this->actingUser();
        SitbTreatmentHistoryClassification::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-treatment-history-classifications')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_treatment_history_classification(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-treatment-history-classifications', ['name' => 'Contoh Sitbklasifikasiriwayatpengobatan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbklasifikasiriwayatpengobatan');

        $this->assertDatabaseHas('sitb_treatment_history_classifications', ['name' => 'Contoh Sitbklasifikasiriwayatpengobatan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbTreatmentHistoryClassification::factory()->create(['name' => 'Contoh Sitbklasifikasiriwayatpengobatan']);

        $this->postJson('/api/v1/sitb-treatment-history-classifications', ['name' => 'Contoh Sitbklasifikasiriwayatpengobatan'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_treatment_history_classification(): void
    {
        $this->actingUser();
        $record = SitbTreatmentHistoryClassification::factory()->create();

        $this->deleteJson("/api/v1/sitb-treatment-history-classifications/{$record->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_treatment_history_classifications', ['id' => $record->id]);
    }
}