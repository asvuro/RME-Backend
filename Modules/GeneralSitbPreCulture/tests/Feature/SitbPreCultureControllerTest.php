<?php

namespace Modules\GeneralSitbPreCulture\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbPreCulture\Models\SitbPreCulture;
use Tests\TestCase;

class SitbPreCultureControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_sitb_pre_culture(): void
    {
        $this->actingUser();
        SitbPreCulture::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-pre-cultures')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_pre_culture(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-pre-cultures', ['name' => 'Contoh Sitbbiakansebelum', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbbiakansebelum');

        $this->assertDatabaseHas('sitb_pre_cultures', ['name' => 'Contoh Sitbbiakansebelum']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbPreCulture::factory()->create(['name' => 'Contoh Sitbbiakansebelum']);

        $this->postJson('/api/v1/sitb-pre-cultures', ['name' => 'Contoh Sitbbiakansebelum'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_pre_culture(): void
    {
        $this->actingUser();
        $sitbPreCulture = SitbPreCulture::factory()->create();

        $this->deleteJson("/api/v1/sitb-pre-cultures/{$sitbPreCulture->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_pre_cultures', ['id' => $sitbPreCulture->id]);
    }
}