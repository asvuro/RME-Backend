<?php

namespace Modules\LayananDrugInteractionCheck\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\InventoryItem\Database\Factories\ItemFactory;
use Modules\LayananDrugInteractionCheck\Models\DrugInteractionRule;
use Modules\LayananDrugInteractionCheck\Services\DrugInteractionCheckService;
use Modules\LayananPrescription\Models\Prescription;
use Modules\LayananPrescriptionItem\Models\PrescriptionItem;
use Modules\GeneralPatient\Models\Patient;
use Modules\MedicalRecordAllergy\Models\Allergy;
use Tests\TestCase;

class DrugInteractionCheckServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    /**
     * Matriks rule engine: rule dibaca dua arah (A-B dan B-A), item resep aktif
     * lain pada visit yang sama <= 48 jam ikut diperiksa, sementara resep
     * > 48 jam, resep visit lain, dan resep cancelled tidak boleh muncul.
     */
    public function test_it_detects_interactions_in_current_and_recent_same_visit_prescriptions_only(): void
    {
        $warfarin = ItemFactory::new()->create(['name' => 'Warfarin 5mg']);
        $amox = ItemFactory::new()->create(['name' => 'Amoxicillin 500mg']);
        $ibuprofen = ItemFactory::new()->create(['name' => 'Ibuprofen 400mg']);
        $cetirizine = ItemFactory::new()->create(['name' => 'Cetirizine 10mg']);
        $metformin = ItemFactory::new()->create(['name' => 'Metformin 500mg']);
        $diazepam = ItemFactory::new()->create(['name' => 'Diazepam 5mg']);

        // Rule disimpan dengan orientasi beragam utk membuktikan pencocokan dua arah:
        // A-B (amox-warfarin) dan B-A (ibuprofen-amox).
        DrugInteractionRule::factory()->create([
            'item_id_a' => $amox->id,
            'item_id_b' => $warfarin->id,
            'severity' => 'major_contraindicated',
            'clinical_note' => 'Risiko perdarahan.',
        ]);
        DrugInteractionRule::factory()->create([
            'item_id_a' => $ibuprofen->id,
            'item_id_b' => $amox->id,
            'severity' => 'moderate',
            'clinical_note' => 'Irerasiasi lambung.',
        ]);
        // Rule yang TIDAK boleh terpicu (lihat skenario di bawah).
        DrugInteractionRule::factory()->create([
            'item_id_a' => $amox->id,
            'item_id_b' => $cetirizine->id,
            'severity' => 'minor',
            'clinical_note' => 'sama visit tapi sudah lewat 48 jam.',
        ]);
        DrugInteractionRule::factory()->create([
            'item_id_a' => $warfarin->id,
            'item_id_b' => $metformin->id,
            'severity' => 'moderate',
            'clinical_note' => 'visit berbeda.',
        ]);
        DrugInteractionRule::factory()->create([
            'item_id_a' => $amox->id,
            'item_id_b' => $diazepam->id,
            'severity' => 'minor',
            'clinical_note' => 'resep cancelled.',
        ]);

        $prescription = Prescription::factory()->create();

        // Resep yang sedang dicek: Warfarin + Amoxicillin.
        PrescriptionItem::factory()->create([
            'prescription_id' => $prescription->id,
            'item_id' => $warfarin->id,
            'drug_name' => 'Warfarin 5mg',
        ]);
        PrescriptionItem::factory()->create([
            'prescription_id' => $prescription->id,
            'item_id' => $amox->id,
            'drug_name' => 'Amoxicillin 500mg',
        ]);

        // Resep lain visit sama, 2 jam lalu, sudah dispensed: Ibuprofen -> kena.
        $recentPrescription = Prescription::factory()->create([
            'visit_id' => $prescription->visit_id,
            'status' => 'dispensed',
            'prescribed_at' => now()->subHours(2),
        ]);
        PrescriptionItem::factory()->create([
            'prescription_id' => $recentPrescription->id,
            'item_id' => $ibuprofen->id,
            'drug_name' => 'Ibuprofen 400mg',
        ]);

        // Visit sama tapi 72 jam lalu: Cetirizine -> tidak boleh masuk pool.
        $oldPrescription = Prescription::factory()->create([
            'visit_id' => $prescription->visit_id,
            'status' => 'active',
            'prescribed_at' => now()->subHours(72),
        ]);
        PrescriptionItem::factory()->create([
            'prescription_id' => $oldPrescription->id,
            'item_id' => $cetirizine->id,
            'drug_name' => 'Cetirizine 10mg',
        ]);

        // Visit LAIN 1 jam lalu: Metformin -> tidak boleh masuk pool.
        $otherVisitPrescription = Prescription::factory()->create([
            'status' => 'active',
            'prescribed_at' => now()->subHour(),
        ]);
        PrescriptionItem::factory()->create([
            'prescription_id' => $otherVisitPrescription->id,
            'item_id' => $metformin->id,
            'drug_name' => 'Metformin 500mg',
        ]);

        // Visit sama, 1 jam lalu, tapi CANCELLED: Diazepam -> tidak dihitung.
        $cancelledPrescription = Prescription::factory()->create([
            'visit_id' => $prescription->visit_id,
            'status' => 'cancelled',
            'prescribed_at' => now()->subHour(),
        ]);
        PrescriptionItem::factory()->create([
            'prescription_id' => $cancelledPrescription->id,
            'item_id' => $diazepam->id,
            'drug_name' => 'Diazepam 5mg',
        ]);

        $findings = app(DrugInteractionCheckService::class)->checkPrescription($prescription->id);

        $this->assertCount(2, $findings);
        $this->assertSame('interaction', $findings[0]['type']);
        // Terberat dulu: major_contraindicated sebelum moderate.
        $this->assertSame('major_contraindicated', $findings[0]['severity']);
        $this->assertStringContainsString('Warfarin', $findings[0]['message']);

        $this->assertSame('moderate', $findings[1]['severity']);
        $this->assertStringContainsString('Ibuprofen', $findings[1]['message']);
        // Pasangan kedua lahir dari item resep lain dalam jendela 48 jam.
        $this->assertStringContainsString('resep lain', $findings[1]['message']);
    }

    /** Pencocokan alergi teks dua arah; alergi inactive tidak dilaporkan. */
    public function test_it_reports_active_allergy_and_skips_inactive_one(): void
    {
        $patient = Patient::factory()->create();
        $amox = ItemFactory::new()->create(['name' => 'Amoxicillin 500mg']);
        $paracetamol = ItemFactory::new()->create(['name' => 'Paracetamol Tablet']);

        Allergy::factory()->create([
            'patient_id' => $patient->id,
            'category' => 'drug',
            'allergen' => 'Amoxicillin',
            'reaction' => 'Urtikaria',
            'severity' => 'severe',
            'is_active' => true,
        ]);
        Allergy::factory()->create([
            'patient_id' => $patient->id,
            'category' => 'drug',
            'allergen' => 'Paracetamol',
            'severity' => 'severe',
            'is_active' => false,
        ]);

        $prescription = Prescription::factory()->create();
        $prescription->visit->registration->update(['patient_id' => $patient->id]);

        PrescriptionItem::factory()->create([
            'prescription_id' => $prescription->id,
            'item_id' => $amox->id,
            'drug_name' => 'Amoxicillin 500mg',
        ]);
        PrescriptionItem::factory()->create([
            'prescription_id' => $prescription->id,
            'item_id' => $paracetamol->id,
            'drug_name' => 'Paracetamol Tablet',
        ]);

        $findings = app(DrugInteractionCheckService::class)->checkPrescription($prescription->id);

        $this->assertCount(1, $findings);
        $this->assertSame('allergy', $findings[0]['type']);
        $this->assertSame('severe', $findings[0]['severity']);
        $this->assertStringContainsString('Amoxicillin', $findings[0]['message']);
        $this->assertStringContainsString('Urtikaria', $findings[0]['message']);
    }

    /** Endpoint advisory read-only: petugas dapat daftar temuan tanpa mengubah apa pun. */
    public function test_check_endpoint_returns_findings_for_petugas(): void
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');

        $a = ItemFactory::new()->create(['name' => 'Warfarin 5mg']);
        $b = ItemFactory::new()->create(['name' => 'Amoxicillin 500mg']);
        DrugInteractionRule::factory()->create([
            'item_id_a' => $a->id,
            'item_id_b' => $b->id,
            'severity' => 'major_contraindicated',
            'clinical_note' => 'Risiko perdarahan.',
        ]);

        $prescription = Prescription::factory()->create();
        PrescriptionItem::factory()->create(['prescription_id' => $prescription->id, 'item_id' => $a->id]);
        PrescriptionItem::factory()->create(['prescription_id' => $prescription->id, 'item_id' => $b->id]);

        $this->getJson("/api/v1/prescriptions/{$prescription->id}/interaction-check")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.type', 'interaction')
            ->assertJsonPath('data.0.severity', 'major_contraindicated');

        // Modul ini advisory: status resep tidak boleh berubah akibat cek.
        $this->assertSame('active', $prescription->fresh()->status);
    }
}
