<?php

namespace Modules\GeneralReturnCancellationReason\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralReturnCancellationReason\Models\ReturnCancellationReason;
use Tests\TestCase;

class ReturnCancellationReasonControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_return_cancellation_reason(): void
    {
        $this->actingUser();
        ReturnCancellationReason::factory()->count(3)->create();

        $this->getJson('/api/v1/return-cancellation-reasons')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_return_cancellation_reason(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/return-cancellation-reasons', ['name' => 'Contoh Alasanpembatalanretur', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Alasanpembatalanretur');

        $this->assertDatabaseHas('return_cancellation_reasons', ['name' => 'Contoh Alasanpembatalanretur']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        ReturnCancellationReason::factory()->create(['name' => 'Contoh Alasanpembatalanretur']);

        $this->postJson('/api/v1/return-cancellation-reasons', ['name' => 'Contoh Alasanpembatalanretur'])->assertStatus(422);
    }

    public function test_it_deletes_return_cancellation_reason(): void
    {
        $this->actingUser();
        $returnCancellationReason = ReturnCancellationReason::factory()->create();

        $this->deleteJson("/api/v1/return-cancellation-reasons/{$returnCancellationReason->id}")->assertStatus(204);
        $this->assertDatabaseMissing('return_cancellation_reasons', ['id' => $returnCancellationReason->id]);
    }
}