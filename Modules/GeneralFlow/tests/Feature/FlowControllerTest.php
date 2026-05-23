<?php

namespace Modules\GeneralFlow\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralFlow\Models\Flow;
use Tests\TestCase;

class FlowControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_flow(): void
    {
        $this->actingUser();
        Flow::factory()->count(3)->create();

        $this->getJson('/api/v1/flows')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_flow(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/flows', ['name' => 'Contoh Referensi', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Referensi');

        $this->assertDatabaseHas('flows', ['name' => 'Contoh Referensi']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        Flow::factory()->create(['name' => 'Contoh Referensi']);

        $this->postJson('/api/v1/flows', ['name' => 'Contoh Referensi'])->assertStatus(422);
    }

    public function test_it_deletes_flow(): void
    {
        $this->actingUser();
        $flow = Flow::factory()->create();

        $this->deleteJson("/api/v1/flows/{$flow->id}")->assertStatus(204);
        $this->assertDatabaseMissing('flows', ['id' => $flow->id]);
    }
}