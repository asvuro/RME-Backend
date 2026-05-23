<?php

namespace Modules\GeneralSitbHivTestResult\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbHivTestResult\Models\SitbHivTestResult;
use Tests\TestCase;

class SitbHivTestResultControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_sitb_hiv_test_result(): void
    {
        $this->actingUser();
        SitbHivTestResult::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-hiv-test-results')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_hiv_test_result(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-hiv-test-results', ['name' => 'Contoh Sitbhasilteshiv', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbhasilteshiv');

        $this->assertDatabaseHas('sitb_hiv_test_results', ['name' => 'Contoh Sitbhasilteshiv']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbHivTestResult::factory()->create(['name' => 'Contoh Sitbhasilteshiv']);

        $this->postJson('/api/v1/sitb-hiv-test-results', ['name' => 'Contoh Sitbhasilteshiv'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_hiv_test_result(): void
    {
        $this->actingUser();
        $sitbHivTestResult = SitbHivTestResult::factory()->create();

        $this->deleteJson("/api/v1/sitb-hiv-test-results/{$sitbHivTestResult->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_hiv_test_results', ['id' => $sitbHivTestResult->id]);
    }
}