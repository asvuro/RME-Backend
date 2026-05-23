<?php

namespace Modules\GeneralSitbReferrerType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbReferrerType\Models\SitbReferrerType;
use Tests\TestCase;

class SitbReferrerTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_sitb_referrer_type(): void
    {
        $this->actingUser();
        SitbReferrerType::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-referrer-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_referrer_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-referrer-types', ['name' => 'Contoh Sitbjenisperujuk', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbjenisperujuk');

        $this->assertDatabaseHas('sitb_referrer_types', ['name' => 'Contoh Sitbjenisperujuk']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbReferrerType::factory()->create(['name' => 'Contoh Sitbjenisperujuk']);

        $this->postJson('/api/v1/sitb-referrer-types', ['name' => 'Contoh Sitbjenisperujuk'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_referrer_type(): void
    {
        $this->actingUser();
        $sitbReferrerType = SitbReferrerType::factory()->create();

        $this->deleteJson("/api/v1/sitb-referrer-types/{$sitbReferrerType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_referrer_types', ['id' => $sitbReferrerType->id]);
    }
}