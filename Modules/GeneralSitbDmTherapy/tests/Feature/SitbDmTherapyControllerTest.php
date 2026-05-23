<?php

namespace Modules\GeneralSitbDmTherapy\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbDmTherapy\Models\SitbDmTherapy;
use Tests\TestCase;

class SitbDmTherapyControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_sitb_dm_therapie(): void
    {
        $this->actingUser();
        SitbDmTherapy::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-dm-therapies')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_dm_therapy(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-dm-therapies', ['name' => 'Contoh Sitbterapidm', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbterapidm');

        $this->assertDatabaseHas('sitb_dm_therapies', ['name' => 'Contoh Sitbterapidm']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbDmTherapy::factory()->create(['name' => 'Contoh Sitbterapidm']);

        $this->postJson('/api/v1/sitb-dm-therapies', ['name' => 'Contoh Sitbterapidm'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_dm_therapy(): void
    {
        $this->actingUser();
        $sitbDmTherapy = SitbDmTherapy::factory()->create();

        $this->deleteJson("/api/v1/sitb-dm-therapies/{$sitbDmTherapy->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_dm_therapies', ['id' => $sitbDmTherapy->id]);
    }
}