<?php

namespace Modules\GeneralOtherStatus\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralOtherStatus\Models\OtherStatus;
use Tests\TestCase;

class OtherStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_other_statuse(): void
    {
        $this->actingUser();
        OtherStatus::factory()->count(3)->create();

        $this->getJson('/api/v1/other-statuses')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_other_status(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/other-statuses', ['name' => 'Contoh Statuslainnya', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Statuslainnya');

        $this->assertDatabaseHas('other_statuses', ['name' => 'Contoh Statuslainnya']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        OtherStatus::factory()->create(['name' => 'Contoh Statuslainnya']);

        $this->postJson('/api/v1/other-statuses', ['name' => 'Contoh Statuslainnya'])->assertStatus(422);
    }

    public function test_it_deletes_other_status(): void
    {
        $this->actingUser();
        $otherStatus = OtherStatus::factory()->create();

        $this->deleteJson("/api/v1/other-statuses/{$otherStatus->id}")->assertStatus(204);
        $this->assertDatabaseMissing('other_statuses', ['id' => $otherStatus->id]);
    }
}