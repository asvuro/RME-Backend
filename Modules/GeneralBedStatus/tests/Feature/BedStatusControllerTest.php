<?php

namespace Modules\GeneralBedStatus\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralBedStatus\Models\BedStatus;
use Tests\TestCase;

class BedStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_bed_statuse(): void
    {
        $this->actingUser();
        BedStatus::factory()->count(3)->create();

        $this->getJson('/api/v1/bed-statuses')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_bed_status(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/bed-statuses', ['name' => 'Contoh Statusruangkamartidur', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Statusruangkamartidur');

        $this->assertDatabaseHas('bed_statuses', ['name' => 'Contoh Statusruangkamartidur']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        BedStatus::factory()->create(['name' => 'Contoh Statusruangkamartidur']);

        $this->postJson('/api/v1/bed-statuses', ['name' => 'Contoh Statusruangkamartidur'])->assertStatus(422);
    }

    public function test_it_deletes_bed_status(): void
    {
        $this->actingUser();
        $bedStatus = BedStatus::factory()->create();

        $this->deleteJson("/api/v1/bed-statuses/{$bedStatus->id}")->assertStatus(204);
        $this->assertDatabaseMissing('bed_statuses', ['id' => $bedStatus->id]);
    }
}