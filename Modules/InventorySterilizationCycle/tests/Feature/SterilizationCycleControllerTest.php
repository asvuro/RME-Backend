<?php

namespace Modules\InventorySterilizationCycle\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\InventorySterilizationCycle\Models\SterilizationCycle;
use Tests\TestCase;

class SterilizationCycleControllerTest extends TestCase
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

    public function test_it_creates_sterilization_cycle_with_generated_cycle_number(): void
    {
        $this->actingUser();

        $response = $this->postJson('/api/v1/sterilization-cycles', [
            'machine_name' => 'Autoklaf 1',
            'temperature_celsius' => 132.5,
            'pressure_bar' => 2.1,
            'duration_minutes' => 30,
            'started_at' => now()->toDateTimeString(),
        ]);

        $response->assertCreated();
        $this->assertStringStartsWith('CYC-', $response->json('cycle_number'));
        $this->assertSame('in_process', $response->json('status'));
        $this->assertSame('pending', $response->json('biological_indicator_result'));
    }

    public function test_it_lists_sterilization_cycles_filtered_by_status(): void
    {
        $this->actingUser();

        SterilizationCycle::factory()->count(2)->create(['status' => SterilizationCycle::STATUS_IN_PROCESS]);
        SterilizationCycle::factory()->passed()->create();

        $response = $this->getJson('/api/v1/sterilization-cycles?status=in_process');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }
}
