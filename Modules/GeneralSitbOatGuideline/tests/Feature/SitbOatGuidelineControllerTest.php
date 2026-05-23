<?php

namespace Modules\GeneralSitbOatGuideline\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbOatGuideline\Models\SitbOatGuideline;
use Tests\TestCase;

class SitbOatGuidelineControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_sitb_oat_guideline(): void
    {
        $this->actingUser();
        SitbOatGuideline::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-oat-guidelines')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_oat_guideline(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-oat-guidelines', ['name' => 'Contoh Sitbpaduanoat', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbpaduanoat');

        $this->assertDatabaseHas('sitb_oat_guidelines', ['name' => 'Contoh Sitbpaduanoat']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbOatGuideline::factory()->create(['name' => 'Contoh Sitbpaduanoat']);

        $this->postJson('/api/v1/sitb-oat-guidelines', ['name' => 'Contoh Sitbpaduanoat'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_oat_guideline(): void
    {
        $this->actingUser();
        $sitbOatGuideline = SitbOatGuideline::factory()->create();

        $this->deleteJson("/api/v1/sitb-oat-guidelines/{$sitbOatGuideline->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_oat_guidelines', ['id' => $sitbOatGuideline->id]);
    }
}