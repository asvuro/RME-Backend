<?php

namespace Modules\GeneralSitbDrugSource\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbDrugSource\Models\SitbDrugSource;
use Tests\TestCase;

class SitbDrugSourceControllerTest extends TestCase
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

    public function test_it_lists_sitb_drug_source(): void
    {
        $this->actingUser();
        SitbDrugSource::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-drug-sources')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_drug_source(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-drug-sources', ['name' => 'Contoh Sitbsumberobat', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbsumberobat');

        $this->assertDatabaseHas('sitb_drug_sources', ['name' => 'Contoh Sitbsumberobat']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbDrugSource::factory()->create(['name' => 'Contoh Sitbsumberobat']);

        $this->postJson('/api/v1/sitb-drug-sources', ['name' => 'Contoh Sitbsumberobat'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_drug_source(): void
    {
        $this->actingUser();
        $sitbDrugSource = SitbDrugSource::factory()->create();

        $this->deleteJson("/api/v1/sitb-drug-sources/{$sitbDrugSource->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_drug_sources', ['id' => $sitbDrugSource->id]);
    }
}