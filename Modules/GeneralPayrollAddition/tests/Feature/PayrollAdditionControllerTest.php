<?php

namespace Modules\GeneralPayrollAddition\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralPayrollAddition\Models\PayrollAddition;
use Tests\TestCase;

class PayrollAdditionControllerTest extends TestCase
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

    public function test_it_lists_payroll_addition(): void
    {
        $this->actingUser();
        PayrollAddition::factory()->count(3)->create();

        $this->getJson('/api/v1/payroll-additions')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_payroll_addition(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/payroll-additions', ['name' => 'Contoh Payrollpenambah', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Payrollpenambah');

        $this->assertDatabaseHas('payroll_additions', ['name' => 'Contoh Payrollpenambah']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        PayrollAddition::factory()->create(['name' => 'Contoh Payrollpenambah']);

        $this->postJson('/api/v1/payroll-additions', ['name' => 'Contoh Payrollpenambah'])->assertStatus(422);
    }

    public function test_it_deletes_payroll_addition(): void
    {
        $this->actingUser();
        $payrollAddition = PayrollAddition::factory()->create();

        $this->deleteJson("/api/v1/payroll-additions/{$payrollAddition->id}")->assertStatus(204);
        $this->assertDatabaseMissing('payroll_additions', ['id' => $payrollAddition->id]);
    }
}