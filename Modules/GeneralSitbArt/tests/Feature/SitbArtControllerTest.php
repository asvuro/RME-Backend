<?php

namespace Modules\GeneralSitbArt\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbArt\Models\SitbArt;
use Tests\TestCase;

class SitbArtControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_sitb_art(): void
    {
        $this->actingUser();
        SitbArt::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-arts')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_art(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-arts', ['name' => 'Contoh Referensi', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Referensi');

        $this->assertDatabaseHas('sitb_arts', ['name' => 'Contoh Referensi']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbArt::factory()->create(['name' => 'Contoh Referensi']);

        $this->postJson('/api/v1/sitb-arts', ['name' => 'Contoh Referensi'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_art(): void
    {
        $this->actingUser();
        $sitbArt = SitbArt::factory()->create();

        $this->deleteJson("/api/v1/sitb-arts/{$sitbArt->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_arts', ['id' => $sitbArt->id]);
    }
}