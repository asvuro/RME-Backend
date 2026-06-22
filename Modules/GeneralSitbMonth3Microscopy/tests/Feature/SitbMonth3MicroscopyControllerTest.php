<?php

namespace Modules\GeneralSitbMonth3Microscopy\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbMonth3Microscopy\Models\SitbMonth3Microscopy;
use Tests\TestCase;

class SitbMonth3MicroscopyControllerTest extends TestCase
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

    public function test_it_lists_sitb_month3_microscopie(): void
    {
        $this->actingUser();
        SitbMonth3Microscopy::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-month3-microscopies')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_month3_microscopy(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-month3-microscopies', ['name' => 'Contoh Sitbmikroskopisbulan3', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbmikroskopisbulan3');

        $this->assertDatabaseHas('sitb_month3_microscopies', ['name' => 'Contoh Sitbmikroskopisbulan3']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbMonth3Microscopy::factory()->create(['name' => 'Contoh Sitbmikroskopisbulan3']);

        $this->postJson('/api/v1/sitb-month3-microscopies', ['name' => 'Contoh Sitbmikroskopisbulan3'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_month3_microscopy(): void
    {
        $this->actingUser();
        $sitbMonth3Microscopy = SitbMonth3Microscopy::factory()->create();

        $this->deleteJson("/api/v1/sitb-month3-microscopies/{$sitbMonth3Microscopy->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_month3_microscopies', ['id' => $sitbMonth3Microscopy->id]);
    }
}