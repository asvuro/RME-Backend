<?php

namespace Modules\GeneralSitbHivStatusClassification\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbHivStatusClassification\Models\SitbHivStatusClassification;
use Tests\TestCase;

class SitbHivStatusClassificationControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_sitb_hiv_status_classification(): void
    {
        $this->actingUser();
        SitbHivStatusClassification::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-hiv-status-classifications')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_hiv_status_classification(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-hiv-status-classifications', ['name' => 'Contoh Sitbklasifikasistatushiv', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbklasifikasistatushiv');

        $this->assertDatabaseHas('sitb_hiv_status_classifications', ['name' => 'Contoh Sitbklasifikasistatushiv']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbHivStatusClassification::factory()->create(['name' => 'Contoh Sitbklasifikasistatushiv']);

        $this->postJson('/api/v1/sitb-hiv-status-classifications', ['name' => 'Contoh Sitbklasifikasistatushiv'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_hiv_status_classification(): void
    {
        $this->actingUser();
        $sitbHivStatusClassification = SitbHivStatusClassification::factory()->create();

        $this->deleteJson("/api/v1/sitb-hiv-status-classifications/{$sitbHivStatusClassification->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_hiv_status_classifications', ['id' => $sitbHivStatusClassification->id]);
    }
}