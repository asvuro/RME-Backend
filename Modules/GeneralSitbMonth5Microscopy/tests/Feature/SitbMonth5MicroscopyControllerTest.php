<?php

namespace Modules\GeneralSitbMonth5Microscopy\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbMonth5Microscopy\Models\SitbMonth5Microscopy;
use Tests\TestCase;

class SitbMonth5MicroscopyControllerTest extends TestCase
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

    public function test_it_lists_sitb_month5_microscopie(): void
    {
        $this->actingUser();
        SitbMonth5Microscopy::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-month5-microscopies')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_month5_microscopy(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-month5-microscopies', ['name' => 'Contoh Sitbmikroskopisbulan5', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbmikroskopisbulan5');

        $this->assertDatabaseHas('sitb_month5_microscopies', ['name' => 'Contoh Sitbmikroskopisbulan5']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbMonth5Microscopy::factory()->create(['name' => 'Contoh Sitbmikroskopisbulan5']);

        $this->postJson('/api/v1/sitb-month5-microscopies', ['name' => 'Contoh Sitbmikroskopisbulan5'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_month5_microscopy(): void
    {
        $this->actingUser();
        $sitbMonth5Microscopy = SitbMonth5Microscopy::factory()->create();

        $this->deleteJson("/api/v1/sitb-month5-microscopies/{$sitbMonth5Microscopy->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_month5_microscopies', ['id' => $sitbMonth5Microscopy->id]);
    }
}