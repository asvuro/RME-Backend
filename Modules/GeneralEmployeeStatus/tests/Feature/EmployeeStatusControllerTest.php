<?php

namespace Modules\GeneralEmployeeStatus\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralEmployeeStatus\Models\EmployeeStatus;
use Tests\TestCase;

class EmployeeStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_employee_statuse(): void
    {
        $this->actingUser();
        EmployeeStatus::factory()->count(3)->create();

        $this->getJson('/api/v1/employee-statuses')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_employee_status(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/employee-statuses', ['name' => 'Contoh Statuspegawai', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Statuspegawai');

        $this->assertDatabaseHas('employee_statuses', ['name' => 'Contoh Statuspegawai']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        EmployeeStatus::factory()->create(['name' => 'Contoh Statuspegawai']);

        $this->postJson('/api/v1/employee-statuses', ['name' => 'Contoh Statuspegawai'])->assertStatus(422);
    }

    public function test_it_deletes_employee_status(): void
    {
        $this->actingUser();
        $employeeStatus = EmployeeStatus::factory()->create();

        $this->deleteJson("/api/v1/employee-statuses/{$employeeStatus->id}")->assertStatus(204);
        $this->assertDatabaseMissing('employee_statuses', ['id' => $employeeStatus->id]);
    }
}