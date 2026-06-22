<?php

namespace Modules\GeneralSitbPreTcm\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbPreTcm\Models\SitbPreTcm;
use Tests\TestCase;

class SitbPreTcmControllerTest extends TestCase
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

    public function test_it_lists_sitb_pre_tcm(): void
    {
        $this->actingUser();
        SitbPreTcm::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-pre-tcms')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_pre_tcm(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-pre-tcms', ['name' => 'Contoh Sitbtcmsebelum', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbtcmsebelum');

        $this->assertDatabaseHas('sitb_pre_tcms', ['name' => 'Contoh Sitbtcmsebelum']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbPreTcm::factory()->create(['name' => 'Contoh Sitbtcmsebelum']);

        $this->postJson('/api/v1/sitb-pre-tcms', ['name' => 'Contoh Sitbtcmsebelum'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_pre_tcm(): void
    {
        $this->actingUser();
        $sitbPreTcm = SitbPreTcm::factory()->create();

        $this->deleteJson("/api/v1/sitb-pre-tcms/{$sitbPreTcm->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_pre_tcms', ['id' => $sitbPreTcm->id]);
    }
}