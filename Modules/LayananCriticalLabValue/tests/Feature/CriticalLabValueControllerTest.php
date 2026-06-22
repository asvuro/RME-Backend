<?php

namespace Modules\LayananCriticalLabValue\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\LayananCriticalLabValue\Models\CriticalLabValue;
use Tests\TestCase;

class CriticalLabValueControllerTest extends TestCase
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

    public function test_it_lists_critical_values(): void
    {
        $this->actingUser();
        CriticalLabValue::factory()->count(3)->create();

        $this->getJson('/api/v1/critical-lab-values')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_critical_value(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/critical-lab-values', [
            'lab_order_id' => \Modules\LayananLabOrder\Models\LabOrder::factory()->create()->id,
            'parameter_name' => 'Test Parameter_name',
            'critical_value' => 'Test Critical_value',
        ])->assertCreated();

        $this->assertDatabaseCount('critical_lab_values', 1);
    }

    public function test_it_shows_critical_value(): void
    {
        $this->actingUser();
        $critical_value = CriticalLabValue::factory()->create();

        $this->getJson("/api/v1/critical-lab-values/{$critical_value->id}")->assertOk()->assertJsonPath('data.id', $critical_value->id);
    }

}
