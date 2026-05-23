<?php

namespace Modules\GeneralSitbAnatomyClassification\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbAnatomyClassification\Models\SitbAnatomyClassification;
use Tests\TestCase;

class SitbAnatomyClassificationControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_sitb_anatomy_classification(): void
    {
        $this->actingUser();
        SitbAnatomyClassification::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-anatomy-classifications')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_anatomy_classification(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-anatomy-classifications', ['name' => 'Contoh Sitbklasifikasilokasianatomi', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbklasifikasilokasianatomi');

        $this->assertDatabaseHas('sitb_anatomy_classifications', ['name' => 'Contoh Sitbklasifikasilokasianatomi']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbAnatomyClassification::factory()->create(['name' => 'Contoh Sitbklasifikasilokasianatomi']);

        $this->postJson('/api/v1/sitb-anatomy-classifications', ['name' => 'Contoh Sitbklasifikasilokasianatomi'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_anatomy_classification(): void
    {
        $this->actingUser();
        $sitbAnatomyClassification = SitbAnatomyClassification::factory()->create();

        $this->deleteJson("/api/v1/sitb-anatomy-classifications/{$sitbAnatomyClassification->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_anatomy_classifications', ['id' => $sitbAnatomyClassification->id]);
    }
}