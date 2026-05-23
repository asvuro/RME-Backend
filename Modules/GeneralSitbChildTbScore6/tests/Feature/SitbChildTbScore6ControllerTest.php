<?php

namespace Modules\GeneralSitbChildTbScore6\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbChildTbScore6\Models\SitbChildTbScore6;
use Tests\TestCase;

class SitbChildTbScore6ControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_sitb_child_tb_score6(): void
    {
        $this->actingUser();
        SitbChildTbScore6::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-child-tb-score6s')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_child_tb_score6(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-child-tb-score6s', ['name' => 'Contoh Sitbscoringtbanak6', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbscoringtbanak6');

        $this->assertDatabaseHas('sitb_child_tb_score6s', ['name' => 'Contoh Sitbscoringtbanak6']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbChildTbScore6::factory()->create(['name' => 'Contoh Sitbscoringtbanak6']);

        $this->postJson('/api/v1/sitb-child-tb-score6s', ['name' => 'Contoh Sitbscoringtbanak6'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_child_tb_score6(): void
    {
        $this->actingUser();
        $sitbChildTbScore6 = SitbChildTbScore6::factory()->create();

        $this->deleteJson("/api/v1/sitb-child-tb-score6s/{$sitbChildTbScore6->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_child_tb_score6s', ['id' => $sitbChildTbScore6->id]);
    }
}