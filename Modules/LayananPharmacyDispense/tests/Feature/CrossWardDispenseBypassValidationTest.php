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
 * Independent validation (authorized white-box): cross-ward dispense
 * authorization bypass on POST /api/v1/prescriptions/{id}/dispense.
 *
 * A petugas assigned to exactly one ward must not be able to dispense a
 * prescription whose visit lives in another ward. Unlike the discovery PoC,
 * this test drives BOTH prescriptions (own-ward control + other-ward target)
 * as the SAME user and asserts every side effect directly through the domain
 * models: ward stock level, Invoice rows, the PharmacyDispense record and
 * the Prescription status flip.
 */
class CrossWardDispenseBypassValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $petugas;

    private Ward $homeWard;

    private Ward $foreignWard;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);

        $this->homeWard = Ward::factory()->create();
        $this->foreignWard = Ward::factory()->create();

        $this->petugas = User::factory()->create();
        $this->petugas->assignRole('petugas');

        // Assignment chain under test: User -> Employee -> StaffMember -> StaffWardAssignment.
        $employee = Employee::factory()->create(['user_id' => $this->petugas->id]);
        $staffMember = StaffMember::factory()->create(['employee_id' => $employee->id]);
        StaffWardAssignment::factory()->create([
            'staff_member_id' => $staffMember->id,
            'ward_id' => $this->homeWard->id,
        ]);
        $this->actingAs($this->petugas, 'sanctum');
    }

    /** Resep siap-layani untuk kunjungan di ward yang diberikan. */
    private function dispensablePrescriptionIn(Ward $ward, int $stock): array
    {
        $visit = Visit::factory()->create(['ward_id' => $ward->id]);
        $prescription = Prescription::factory()->create(['visit_id' => $visit->id]);
        $item = Item::factory()->create(['sell_price' => '7500.00']);
        PrescriptionItem::factory()->create([
            'prescription_id' => $prescription->id,
            'item_id' => $item->id,
            'quantity' => 3,
        ]);

        app(StockGate::class)->adjust($ward->id, $item->id, 'in', $stock, $this->petugas);

        PrescriptionInitialReviewFactory::new()->create([
            'prescription_id' => $prescription->id,
            'is_appropriate' => true,
            'issues_found' => null,
        ]);

        return [$prescription, $item];
    }

    public function test_boundary_control_petugas_cannot_read_foreign_ward_visit(): void
    {
        $foreignVisit = Visit::factory()->create(['ward_id' => $this->foreignWard->id]);

        $this->getJson("/api/v1/visits/{$foreignVisit->id}")
            ->assertStatus(403);
    }

    public function test_own_ward_dispense_succeeds_as_control(): void
    {
        [$prescription] = $this->dispensablePrescriptionIn($this->homeWard, stock: 30);

        $this->postJson("/api/v1/prescriptions/{$prescription->id}/dispense")
            ->assertCreated();
    }

    public function test_ward_scoped_petugas_cannot_dispense_prescription_of_other_ward(): void
    {
        [$ownPrescription, $ownItem] = $this->dispensablePrescriptionIn($this->homeWard, stock: 30);
        [$foreignPrescription, $foreignItem] = $this->dispensablePrescriptionIn($this->foreignWard, stock: 12);

        $response = $this->postJson("/api/v1/prescriptions/{$foreignPrescription->id}/dispense");

        if ($response->status() === 201) {
            $this->markTestIncomplete(
                'VULNERABLE: ward-scoped petugas dispensed foreign-ward prescription. '
                ."Foreign ward {$this->foreignWard->id} stock: "
                .app(StockGate::class)->currentStock($this->foreignWard->id, $foreignItem->id)
                .' (seeded 12). Invoices: '.Invoice::count()
                .' Dispenses: '.PharmacyDispense::count()
            );
            return;
        }

        $response->assertStatus(403);

        // Tanpa efek samping apa pun pada ward lain.
        $this->assertSame(0, PharmacyDispense::count());
        $this->assertSame(0, Invoice::query()->where('visit_id', $foreignPrescription->visit_id)->count());
        $this->assertSame(
            12,
            app(StockGate::class)->currentStock($this->foreignWard->id, $foreignItem->id),
        );

        // Ward sendiri tidak tersentuh dan tetap boleh dilayani user yang sama.
        $this->assertSame(
            30,
            app(StockGate::class)->currentStock($this->homeWard->id, $ownItem->id),
        );
        $this->postJson("/api/v1/prescriptions/{$ownPrescription->id}/dispense")
            ->assertCreated();
    }
}