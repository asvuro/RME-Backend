<?php

namespace Modules\GeneralPayrollDeduction\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralPayrollDeduction\Models\PayrollDeduction;
use Tests\TestCase;

class PayrollDeductionControllerTest extends TestCase
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

    public function test_it_lists_payroll_deduction(): void
    {
        $this->actingUser();
        PayrollDeduction::factory()->count(3)->create();

        $this->getJson('/api/v1/payroll-deductions')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_payroll_deduction(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/payroll-deductions', ['name' => 'Contoh Payrollpengurang', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Payrollpengurang');

        $this->assertDatabaseHas('payroll_deductions', ['name' => 'Contoh Payrollpengurang']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        PayrollDeduction::factory()->create(['name' => 'Contoh Payrollpengurang']);

        $this->postJson('/api/v1/payroll-deductions', ['name' => 'Contoh Payrollpengurang'])->assertStatus(422);
    }

    public function test_it_deletes_payroll_deduction(): void
    {
        $this->actingUser();
        $payrollDeduction = PayrollDeduction::factory()->create();

        $this->deleteJson("/api/v1/payroll-deductions/{$payrollDeduction->id}")->assertStatus(204);
        $this->assertDatabaseMissing('payroll_deductions', ['id' => $payrollDeduction->id]);
    }
}