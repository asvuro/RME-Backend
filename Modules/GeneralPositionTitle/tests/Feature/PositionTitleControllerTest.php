<?php

namespace Modules\GeneralPositionTitle\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralPositionTitle\Models\PositionTitle;
use Tests\TestCase;

class PositionTitleControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_position_title(): void
    {
        $this->actingUser();
        PositionTitle::factory()->count(3)->create();

        $this->getJson('/api/v1/position-titles')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_position_title(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/position-titles', ['name' => 'Contoh Jabatan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jabatan');

        $this->assertDatabaseHas('position_titles', ['name' => 'Contoh Jabatan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        PositionTitle::factory()->create(['name' => 'Contoh Jabatan']);

        $this->postJson('/api/v1/position-titles', ['name' => 'Contoh Jabatan'])->assertStatus(422);
    }

    public function test_it_deletes_position_title(): void
    {
        $this->actingUser();
        $positionTitle = PositionTitle::factory()->create();

        $this->deleteJson("/api/v1/position-titles/{$positionTitle->id}")->assertStatus(204);
        $this->assertDatabaseMissing('position_titles', ['id' => $positionTitle->id]);
    }
}