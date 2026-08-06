<?php

namespace Modules\InventoryDietOrder\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Models\Employee;
use Modules\InventoryDietOrder\Models\DietOrder;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

class DietOrderControllerTest extends TestCase
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

    public function test_it_creates_a_diet_order(): void
    {
        $this->actingUser();
        $visit = Visit::factory()->create();
        $employee = Employee::factory()->create();

        $this->postJson('/api/v1/diet-orders', [
            'visit_id' => $visit->id,
            'diet_type' => 'lunak',
            'calorie_target' => 1800,
            'meal_schedule' => 'siang',
            'ordered_by' => $employee->id,
            'order_date' => '2026-08-26',
        ])
            ->assertCreated()
            ->assertJsonPath('data.diet_type', 'lunak')
            ->assertJsonPath('data.status', DietOrder::STATUS_ORDERED);
    }

    public function test_it_rejects_diet_order_for_discharged_visit(): void
    {
        $this->actingUser();
        $visit = Visit::factory()->create(['discharged_at' => now(), 'status' => 'discharged']);
        $employee = Employee::factory()->create();

        $this->postJson('/api/v1/diet-orders', [
            'visit_id' => $visit->id,
            'diet_type' => 'biasa',
            'meal_schedule' => 'pagi',
            'ordered_by' => $employee->id,
            'order_date' => '2026-08-26',
        ])
            ->assertStatus(422);
    }

    public function test_it_lists_diet_orders_filtered_by_visit_and_order_date(): void
    {
        $this->actingUser();
        $visit = Visit::factory()->create();
        $employee = Employee::factory()->create();

        DietOrder::factory()->count(2)->create([
            'visit_id' => $visit->id,
            'ordered_by' => $employee->id,
            'order_date' => '2026-08-26',
        ]);
        DietOrder::factory()->create(['order_date' => '2026-08-27']);

        $this->getJson("/api/v1/diet-orders?visit_id={$visit->id}&order_date=2026-08-26")
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }
}
