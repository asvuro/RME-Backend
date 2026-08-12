<?php

namespace Modules\PembayaranPatientReceivable\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralPatient\Models\Patient;
use Modules\PembayaranInvoice\Models\Invoice;
use Tests\TestCase;

/**
 * POC: settlement caps anchor to patient_receivables.amount, but that
 * amount is client-supplied at creation with no cross-check against the
 * linked invoice, so petugas can mint an oversized receivable then settle it.
 */
class PatientReceivableAnchorIntegrityPocTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function actingPetugas(): User
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_petugas_cannot_mint_receivable_exceeding_invoice_patient_share(): void
    {
        $this->actingPetugas();
        $invoice = Invoice::factory()->create(['total_amount' => 0]);
        $patient = Patient::factory()->create();

        $response = $this->postJson('/api/v1/patient-receivables', [
            'invoice_id' => $invoice->id,
            'patient_id' => $patient->id,
            'amount' => 999999000,
            'due_date' => now()->addDays(14)->toDateString(),
        ]);

        if ($response->status() === 201) {
            $settlement = $this->postJson('/api/v1/patient-receivable-settlements', [
                'patient_receivable_id' => $response->json('data.id'),
                'paid_amount' => 999999000,
            ]);
            fwrite(STDERR, sprintf(
                "[POC-D] receivable anchor inflated to 999999000 over invoice share 0; settlement -> HTTP %s\n",
                $settlement->status(),
            ));
            $this->fail('[POC-D] petugas minted oversized receivable then settled it');
        }

        $response->assertStatus(422);
    }

    public function test_receivable_within_invoice_patient_share_still_creates(): void
    {
        $this->actingPetugas();
        $invoice = Invoice::factory()->create(['total_amount' => 300000]);
        $patient = Patient::factory()->create();

        $this->postJson('/api/v1/patient-receivables', [
            'invoice_id' => $invoice->id,
            'patient_id' => $patient->id,
            'amount' => 250000,
            'due_date' => now()->addDays(14)->toDateString(),
        ])->assertCreated()->assertJsonPath('data.status', 'outstanding');
    }
}