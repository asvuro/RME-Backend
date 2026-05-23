<?php

namespace Modules\GeneralQuarter\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralQuarter\Models\Quarter;
use Tests\TestCase;

class QuarterControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_quarter(): void
    {
        $this->actingUser();
        Quarter::factory()->count(3)->create();

        $this->getJson('/api/v1/quarters')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_quarter(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/quarters', ['name' => 'Contoh Jenistriwulan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenistriwulan');

        $this->assertDatabaseHas('quarters', ['name' => 'Contoh Jenistriwulan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        Quarter::factory()->create(['name' => 'Contoh Jenistriwulan']);

        $this->postJson('/api/v1/quarters', ['name' => 'Contoh Jenistriwulan'])->assertStatus(422);
    }

    public function test_it_deletes_quarter(): void
    {
        $this->actingUser();
        $quarter = Quarter::factory()->create();

        $this->deleteJson("/api/v1/quarters/{$quarter->id}")->assertStatus(204);
        $this->assertDatabaseMissing('quarters', ['id' => $quarter->id]);
    }
}