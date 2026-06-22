<?php

namespace Modules\GeneralSitbChildTbScore5\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbChildTbScore5\Models\SitbChildTbScore5;
use Tests\TestCase;

class SitbChildTbScore5ControllerTest extends TestCase
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

    public function test_it_lists_sitb_child_tb_score5(): void
    {
        $this->actingUser();
        SitbChildTbScore5::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-child-tb-score5s')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_child_tb_score5(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-child-tb-score5s', ['name' => 'Contoh Sitbscoringtbanak5', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbscoringtbanak5');

        $this->assertDatabaseHas('sitb_child_tb_score5s', ['name' => 'Contoh Sitbscoringtbanak5']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbChildTbScore5::factory()->create(['name' => 'Contoh Sitbscoringtbanak5']);

        $this->postJson('/api/v1/sitb-child-tb-score5s', ['name' => 'Contoh Sitbscoringtbanak5'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_child_tb_score5(): void
    {
        $this->actingUser();
        $sitbChildTbScore5 = SitbChildTbScore5::factory()->create();

        $this->deleteJson("/api/v1/sitb-child-tb-score5s/{$sitbChildTbScore5->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_child_tb_score5s', ['id' => $sitbChildTbScore5->id]);
    }
}