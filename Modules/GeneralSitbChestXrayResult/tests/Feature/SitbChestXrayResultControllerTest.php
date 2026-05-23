<?php

namespace Modules\GeneralSitbChestXrayResult\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbChestXrayResult\Models\SitbChestXrayResult;
use Tests\TestCase;

class SitbChestXrayResultControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_sitb_chest_xray_result(): void
    {
        $this->actingUser();
        SitbChestXrayResult::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-chest-xray-results')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_chest_xray_result(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-chest-xray-results', ['name' => 'Contoh Sitbhasilfototoraks', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbhasilfototoraks');

        $this->assertDatabaseHas('sitb_chest_xray_results', ['name' => 'Contoh Sitbhasilfototoraks']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbChestXrayResult::factory()->create(['name' => 'Contoh Sitbhasilfototoraks']);

        $this->postJson('/api/v1/sitb-chest-xray-results', ['name' => 'Contoh Sitbhasilfototoraks'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_chest_xray_result(): void
    {
        $this->actingUser();
        $sitbChestXrayResult = SitbChestXrayResult::factory()->create();

        $this->deleteJson("/api/v1/sitb-chest-xray-results/{$sitbChestXrayResult->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_chest_xray_results', ['id' => $sitbChestXrayResult->id]);
    }
}