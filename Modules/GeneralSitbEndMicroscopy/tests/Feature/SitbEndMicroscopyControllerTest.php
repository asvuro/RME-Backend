<?php

namespace Modules\GeneralSitbEndMicroscopy\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbEndMicroscopy\Models\SitbEndMicroscopy;
use Tests\TestCase;

class SitbEndMicroscopyControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_sitb_end_microscopie(): void
    {
        $this->actingUser();
        SitbEndMicroscopy::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-end-microscopies')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_end_microscopy(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-end-microscopies', ['name' => 'Contoh Sitbmikroskopisakhir', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbmikroskopisakhir');

        $this->assertDatabaseHas('sitb_end_microscopies', ['name' => 'Contoh Sitbmikroskopisakhir']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbEndMicroscopy::factory()->create(['name' => 'Contoh Sitbmikroskopisakhir']);

        $this->postJson('/api/v1/sitb-end-microscopies', ['name' => 'Contoh Sitbmikroskopisakhir'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_end_microscopy(): void
    {
        $this->actingUser();
        $sitbEndMicroscopy = SitbEndMicroscopy::factory()->create();

        $this->deleteJson("/api/v1/sitb-end-microscopies/{$sitbEndMicroscopy->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_end_microscopies', ['id' => $sitbEndMicroscopy->id]);
    }
}