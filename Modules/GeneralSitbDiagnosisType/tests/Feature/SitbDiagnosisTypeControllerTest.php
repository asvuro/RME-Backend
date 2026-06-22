<?php

namespace Modules\GeneralSitbDiagnosisType\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbDiagnosisType\Models\SitbDiagnosisType;
use Tests\TestCase;

class SitbDiagnosisTypeControllerTest extends TestCase
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

    public function test_it_lists_sitb_diagnosis_type(): void
    {
        $this->actingUser();
        SitbDiagnosisType::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-diagnosis-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_diagnosis_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-diagnosis-types', ['name' => 'Contoh Sitbjenisdiagnosis', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbjenisdiagnosis');

        $this->assertDatabaseHas('sitb_diagnosis_types', ['name' => 'Contoh Sitbjenisdiagnosis']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbDiagnosisType::factory()->create(['name' => 'Contoh Sitbjenisdiagnosis']);

        $this->postJson('/api/v1/sitb-diagnosis-types', ['name' => 'Contoh Sitbjenisdiagnosis'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_diagnosis_type(): void
    {
        $this->actingUser();
        $sitbDiagnosisType = SitbDiagnosisType::factory()->create();

        $this->deleteJson("/api/v1/sitb-diagnosis-types/{$sitbDiagnosisType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_diagnosis_types', ['id' => $sitbDiagnosisType->id]);
    }
}