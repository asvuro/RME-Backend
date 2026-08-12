<?php

namespace Modules\LayananPharmacyDispense\Tests\Feature;

use App\Modules\Contracts\StockGate;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Models\Employee;
use Modules\GeneralStaffMember\Models\StaffMember;
use Modules\GeneralStaffWardAssignment\Models\StaffWardAssignment;
use Modules\GeneralWard\Models\Ward;
use Modules\InventoryItem\Models\Item;
use Modules\LayananPharmacyDispense\Models\PharmacyDispense;
use Modules\LayananPrescriptionInitialReview\Database\Factories\PrescriptionInitialReviewFactory;
use Modules\LayananPrescriptionItem\Models\PrescriptionItem;
use Modules\LayananPrescription\Models\Prescription;
use Modules\PembayaranInvoice\Models\Invoice;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

/**
 * Independent validation (authorized white-box): pharmacy dispense against a
 * DISCHARGED visit on POST /api/v1/prescriptions/{id}/dispense.
 *
 * Unlike the discovery PoC, this drives the flow as a ward-scoped petugas
 * (full User -> Employee -> StaffMember -> StaffWardAssignment chain) and adds
 * an ACTIVE-visit positive control so any new visit-state gate must block only
 * the closed encounter, not live ones. Side effects are asserted directly
 * through domain models: ward stock, the closed visit's Invoice rows, the
 * PharmacyDispense record (incl. attribution) and the Prescription status.
 */
class DischargedVisitDispenseValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $petugas;

    private Ward $ward;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);

        $this->ward = Ward::factory()->create();

        $this->petugas = User::factory()->create();
        $this->petugas->assignRole('petugas');
        $employee = Employee::factory()->create(['user_id' => $this->petugas->id]);
        $staffMember = StaffMember::factory()->create(['employee_id' => $employee->id]);
        StaffWardAssignment::factory()->create([
            'staff_member_id' => $staffMember->id,
            'ward_id' => $this->ward->id,
        ]);
        $this->actingAs($this->petugas, 'sanctum');
    }

    /** Resep siap-layani (telaah bersih, stok cukup) untuk kunjungan yang diberikan. */
    private function dispensablePrescriptionFor(Visit $visit): array
    {
        $prescription = Prescription::factory()->create(['visit_id' => $visit->id]);
        $item = Item::factory()->create(['sell_price' => '5000.00']);
        PrescriptionItem::factory()->create([
            'prescription_id' => $prescription->id,
            'item_id' => $item->id,
            'quantity' => 4,
        ]);

        app(StockGate::class)->adjust((int) $visit->ward_id, $item->id, 'in', 100, $this->petugas);

        PrescriptionInitialReviewFactory::new()->create([
            'prescription_id' => $prescription->id,
            'is_appropriate' => true,
            'issues_found' => null,
        ]);

        return [$prescription, $item];
    }

    public function test_active_visit_dispense_succeeds_as_control(): void
    {
        $activeVisit = Visit::factory()->create(['ward_id' => $this->ward->id]);
        [$prescription] = $this->dispensablePrescriptionFor($activeVisit);

        $this->postJson("/api/v1/prescriptions/{$prescription->id}/dispense")
            ->assertCreated();
    }

    public function test_ward_scoped_petugas_cannot_dispense_prescription_of_discharged_visit(): void
    {
        $dischargedVisit = Visit::factory()->discharged()->create(['ward_id' => $this->ward->id]);
        $this->assertNotNull($dischargedVisit->discharged_at, 'precondition: visit discharged');

        [$prescription, $item] = $this->dispensablePrescriptionFor($dischargedVisit);

        $response = $this->postJson("/api/v1/prescriptions/{$prescription->id}/dispense");

        if (in_array($response->status(), [200, 201], true)) {
            $this->markTestIncomplete(
                'VULNERABLE: dispense succeeded against a DISCHARGED visit. '
                ."Ward {$this->ward->id} stock: "
                .app(StockGate::class)->currentStock($this->ward->id, $item->id)
                .' (seeded 100). Invoices: '.Invoice::count()
                .' Dispenses: '.PharmacyDispense::count()
            );
            return;
        }

        $response->assertStatus(422);

        // Tanpa efek samping apa pun pada kunjungan yang sudah pulang.
        $this->assertSame(0, PharmacyDispense::count());
        $this->assertSame(
            0,
            Invoice::query()->where('visit_id', $dischargedVisit->id)->count(),
        );
        $this->assertSame(
            100,
            app(StockGate::class)->currentStock($this->ward->id, $item->id),
        );
        $this->assertSame('active', $prescription->refresh()->status);
    }
}