<?php

namespace Modules\GeneralSitbThoraxNotDone\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbThoraxNotDone\Models\SitbThoraxNotDone;
use Tests\TestCase;

class SitbThoraxNotDoneControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_sitb_thorax_not_done(): void
    {
        $this->actingUser();
        SitbThoraxNotDone::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-thorax-not-dones')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_thorax_not_done(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-thorax-not-dones', ['name' => 'Contoh Sitbtorakstidakdilakukan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbtorakstidakdilakukan');

        $this->assertDatabaseHas('sitb_thorax_not_dones', ['name' => 'Contoh Sitbtorakstidakdilakukan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbThoraxNotDone::factory()->create(['name' => 'Contoh Sitbtorakstidakdilakukan']);

        $this->postJson('/api/v1/sitb-thorax-not-dones', ['name' => 'Contoh Sitbtorakstidakdilakukan'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_thorax_not_done(): void
    {
        $this->actingUser();
        $sitbThoraxNotDone = SitbThoraxNotDone::factory()->create();

        $this->deleteJson("/api/v1/sitb-thorax-not-dones/{$sitbThoraxNotDone->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_thorax_not_dones', ['id' => $sitbThoraxNotDone->id]);
    }
}