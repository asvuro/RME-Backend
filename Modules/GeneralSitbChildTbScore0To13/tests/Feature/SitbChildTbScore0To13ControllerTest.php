<?php

namespace Modules\GeneralSitbChildTbScore0To13\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbChildTbScore0To13\Models\SitbChildTbScore0To13;
use Tests\TestCase;

class SitbChildTbScore0To13ControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_sitb_child_tb_score0_to13(): void
    {
        $this->actingUser();
        SitbChildTbScore0To13::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-child-tb-score0-to13s')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_child_tb_score0_to13(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-child-tb-score0-to13s', ['name' => 'Contoh Sitbscoringtbanak0to13', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbscoringtbanak0to13');

        $this->assertDatabaseHas('sitb_child_tb_score0_to13s', ['name' => 'Contoh Sitbscoringtbanak0to13']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbChildTbScore0To13::factory()->create(['name' => 'Contoh Sitbscoringtbanak0to13']);

        $this->postJson('/api/v1/sitb-child-tb-score0-to13s', ['name' => 'Contoh Sitbscoringtbanak0to13'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_child_tb_score0_to13(): void
    {
        $this->actingUser();
        $sitbChildTbScore0To13 = SitbChildTbScore0To13::factory()->create();

        $this->deleteJson("/api/v1/sitb-child-tb-score0-to13s/{$sitbChildTbScore0To13->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_child_tb_score0_to13s', ['id' => $sitbChildTbScore0To13->id]);
    }
}