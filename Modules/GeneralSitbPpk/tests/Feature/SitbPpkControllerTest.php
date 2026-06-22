<?php

namespace Modules\GeneralSitbPpk\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbPpk\Models\SitbPpk;
use Tests\TestCase;

class SitbPpkControllerTest extends TestCase
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

    public function test_it_lists_sitb_ppk(): void
    {
        $this->actingUser();
        SitbPpk::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-ppks')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_ppk(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-ppks', ['name' => 'Contoh Referensi', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Referensi');

        $this->assertDatabaseHas('sitb_ppks', ['name' => 'Contoh Referensi']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbPpk::factory()->create(['name' => 'Contoh Referensi']);

        $this->postJson('/api/v1/sitb-ppks', ['name' => 'Contoh Referensi'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_ppk(): void
    {
        $this->actingUser();
        $sitbPpk = SitbPpk::factory()->create();

        $this->deleteJson("/api/v1/sitb-ppks/{$sitbPpk->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_ppks', ['id' => $sitbPpk->id]);
    }
}