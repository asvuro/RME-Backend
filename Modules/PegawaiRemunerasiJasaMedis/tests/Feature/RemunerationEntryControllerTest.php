<?php

namespace Modules\PegawaiRemunerasiJasaMedis\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Models\Employee;
use Modules\PegawaiRemunerasiJasaMedis\Models\RemunerationEntry;
use Tests\TestCase;

class RemunerationEntryControllerTest extends TestCase
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

    /** (a) create/store berhasil, net_amount ikut dihitung & dikembalikan. */
    public function test_it_creates_remuneration_entry_with_calculated_net_amount(): void
    {
        $this->actingUser();
        $employee = Employee::factory()->create();

        $response = $this->postJson('/api/v1/remuneration-entries', [
            'employee_id' => $employee->id,
            'source_type' => 'tindakan',
            'source_id' => 55,
            'role' => 'operator_utama',
            'gross_amount' => 1000000,
            'deduction_percentage' => 10,
            'fixed_deduction' => 50000,
            'service_date' => '2026-08-20',
        ]);

        $response->assertCreated();
        // net = 1.000.000 - (1.000.000 * 10%) - 50.000 = 850.000
        $this->assertEquals(850000, (float) $response->json('net_amount'));
    }

    /** (b) gerbang bisnis: net negatif (potongan melebihi gross) ditolak 422. */
    public function test_it_rejects_entry_when_net_amount_would_be_negative(): void
    {
        $this->actingUser();
        $employee = Employee::factory()->create();

        $this->postJson('/api/v1/remuneration-entries', [
            'employee_id' => $employee->id,
            'source_type' => 'tindakan',
            'source_id' => 55,
            'role' => 'operator_utama',
            'gross_amount' => 100000,
            'deduction_percentage' => 50,
            'fixed_deduction' => 90000,
            'service_date' => '2026-08-20',
        ])->assertStatus(422);
    }

    /** (b) gerbang bisnis: formula deduction_percentage benar tanpa fixed_deduction. */
    public function test_calculation_is_correct_with_percentage_deduction_only(): void
    {
        $this->actingUser();
        $employee = Employee::factory()->create();

        $response = $this->postJson('/api/v1/remuneration-entries', [
            'employee_id' => $employee->id,
            'source_type' => 'invoice_item',
            'source_id' => 7,
            'role' => 'anestesi',
            'gross_amount' => 500000,
            'deduction_percentage' => 20,
            'service_date' => '2026-08-15',
        ]);

        $response->assertCreated();
        // net = 500.000 - (500.000 * 20%) - 0 = 400.000
        $this->assertEquals(400000, (float) $response->json('net_amount'));
    }

    /** (c) list/index. */
    public function test_it_lists_remuneration_entries_filtered_by_employee(): void
    {
        $this->actingUser();
        $employee = Employee::factory()->create();
        RemunerationEntry::factory()->count(2)->create(['employee_id' => $employee->id]);
        RemunerationEntry::factory()->create();

        $this->getJson("/api/v1/remuneration-entries?employee_id={$employee->id}")
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    /** Summary per pegawai per periode menjumlahkan gross & net dengan benar. */
    public function test_summary_endpoint_totals_gross_and_net_for_period(): void
    {
        $this->actingUser();
        $employee = Employee::factory()->create();

        RemunerationEntry::factory()->create([
            'employee_id' => $employee->id,
            'service_date' => '2026-08-05',
            'gross_amount' => 1000000,
            'deduction_percentage' => 10,
            'fixed_deduction' => 0,
            'net_amount' => 900000,
        ]);
        RemunerationEntry::factory()->create([
            'employee_id' => $employee->id,
            'service_date' => '2026-08-18',
            'gross_amount' => 500000,
            'deduction_percentage' => 0,
            'fixed_deduction' => 0,
            'net_amount' => 500000,
        ]);
        // Di luar periode — tidak boleh ikut terhitung.
        RemunerationEntry::factory()->create([
            'employee_id' => $employee->id,
            'service_date' => '2026-07-05',
            'gross_amount' => 999999,
            'net_amount' => 999999,
        ]);

        $response = $this->getJson("/api/v1/remuneration-entries/summary?employee_id={$employee->id}&month=8&year=2026");

        $response->assertOk();
        $this->assertEquals(2, $response->json('entry_count'));
        $this->assertEquals(1500000, (float) $response->json('total_gross'));
        $this->assertEquals(1400000, (float) $response->json('total_net'));
    }
}
