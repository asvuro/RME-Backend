<?php

namespace Modules\GeneralReturnCancellationType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralReturnCancellationType\Models\ReturnCancellationType;
use Tests\TestCase;

class ReturnCancellationTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_return_cancellation_type(): void
    {
        $this->actingUser();
        ReturnCancellationType::factory()->count(3)->create();

        $this->getJson('/api/v1/return-cancellation-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_return_cancellation_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/return-cancellation-types', ['name' => 'Contoh Jenispembatalanretur', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenispembatalanretur');

        $this->assertDatabaseHas('return_cancellation_types', ['name' => 'Contoh Jenispembatalanretur']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        ReturnCancellationType::factory()->create(['name' => 'Contoh Jenispembatalanretur']);

        $this->postJson('/api/v1/return-cancellation-types', ['name' => 'Contoh Jenispembatalanretur'])->assertStatus(422);
    }

    public function test_it_deletes_return_cancellation_type(): void
    {
        $this->actingUser();
        $returnCancellationType = ReturnCancellationType::factory()->create();

        $this->deleteJson("/api/v1/return-cancellation-types/{$returnCancellationType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('return_cancellation_types', ['id' => $returnCancellationType->id]);
    }
}