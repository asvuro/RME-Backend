<?php

namespace Modules\GeneralSitbDm\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbDm\Models\SitbDm;
use Tests\TestCase;

class SitbDmControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_sitb_dm(): void
    {
        $this->actingUser();
        SitbDm::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-dms')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_dm(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-dms', ['name' => 'Contoh Referensi', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Referensi');

        $this->assertDatabaseHas('sitb_dms', ['name' => 'Contoh Referensi']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbDm::factory()->create(['name' => 'Contoh Referensi']);

        $this->postJson('/api/v1/sitb-dms', ['name' => 'Contoh Referensi'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_dm(): void
    {
        $this->actingUser();
        $sitbDm = SitbDm::factory()->create();

        $this->deleteJson("/api/v1/sitb-dms/{$sitbDm->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_dms', ['id' => $sitbDm->id]);
    }
}