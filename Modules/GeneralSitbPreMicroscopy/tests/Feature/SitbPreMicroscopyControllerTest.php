<?php

namespace Modules\GeneralSitbPreMicroscopy\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbPreMicroscopy\Models\SitbPreMicroscopy;
use Tests\TestCase;

class SitbPreMicroscopyControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_sitb_pre_microscopie(): void
    {
        $this->actingUser();
        SitbPreMicroscopy::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-pre-microscopies')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_pre_microscopy(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-pre-microscopies', ['name' => 'Contoh Sitbmikroskopissebelum', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbmikroskopissebelum');

        $this->assertDatabaseHas('sitb_pre_microscopies', ['name' => 'Contoh Sitbmikroskopissebelum']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbPreMicroscopy::factory()->create(['name' => 'Contoh Sitbmikroskopissebelum']);

        $this->postJson('/api/v1/sitb-pre-microscopies', ['name' => 'Contoh Sitbmikroskopissebelum'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_pre_microscopy(): void
    {
        $this->actingUser();
        $sitbPreMicroscopy = SitbPreMicroscopy::factory()->create();

        $this->deleteJson("/api/v1/sitb-pre-microscopies/{$sitbPreMicroscopy->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_pre_microscopies', ['id' => $sitbPreMicroscopy->id]);
    }
}