<?php

namespace Modules\GeneralSitbMonth2Microscopy\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbMonth2Microscopy\Models\SitbMonth2Microscopy;
use Tests\TestCase;

class SitbMonth2MicroscopyControllerTest extends TestCase
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

    public function test_it_lists_sitb_month2_microscopie(): void
    {
        $this->actingUser();
        SitbMonth2Microscopy::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-month2-microscopies')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_month2_microscopy(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-month2-microscopies', ['name' => 'Contoh Sitbmikroskopisbulan2', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbmikroskopisbulan2');

        $this->assertDatabaseHas('sitb_month2_microscopies', ['name' => 'Contoh Sitbmikroskopisbulan2']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbMonth2Microscopy::factory()->create(['name' => 'Contoh Sitbmikroskopisbulan2']);

        $this->postJson('/api/v1/sitb-month2-microscopies', ['name' => 'Contoh Sitbmikroskopisbulan2'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_month2_microscopy(): void
    {
        $this->actingUser();
        $sitbMonth2Microscopy = SitbMonth2Microscopy::factory()->create();

        $this->deleteJson("/api/v1/sitb-month2-microscopies/{$sitbMonth2Microscopy->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_month2_microscopies', ['id' => $sitbMonth2Microscopy->id]);
    }
}