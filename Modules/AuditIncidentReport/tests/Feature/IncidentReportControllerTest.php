<?php

namespace Modules\AuditIncidentReport\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AuditIncidentReport\Models\IncidentReport;
use Modules\AuditIncidentReport\Services\IncidentReportService;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Database\Factories\EmployeeFactory;
use Tests\TestCase;

class IncidentReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function actingUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    private function storePayload(array $overrides = []): array
    {
        return [
            'incident_category' => 'KTD',
            'description' => 'Pasien terjatuh dari bed di ruang rawat.',
            'occurred_at' => '2026-08-20 10:00:00',
            'reported_by' => EmployeeFactory::new()->create()->id,
            'impact_score' => 4,
            'probability_score' => 5,
            ...$overrides,
        ];
    }

    /** (c) List/index. */
    public function test_it_lists_incident_reports(): void
    {
        $this->actingUser();
        IncidentReport::factory()->count(3)->create();

        $this->getJson('/api/v1/incident-reports')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    /** (a) Store sukses oleh petugas + grade/SLA/status otomatis dari service. */
    public function test_petugas_can_store_incident_report_and_service_computes_grade_and_sla(): void
    {
        $this->actingUser();

        // 4 x 5 = 20 → MERAH, SLA KUNING/MERAH = occurred_at + 45 hari.
        // Respons single JsonResource dibungkus key "data" secara default.
        $this->postJson('/api/v1/incident-reports', $this->storePayload())
            ->assertCreated()
            ->assertJsonPath('data.risk_grade', 'MERAH')
            ->assertJsonPath('data.status', 'reported')
            ->assertJsonPath('data.impact_score', 4);

        $this->assertDatabaseHas('incident_reports', [
            'incident_category' => 'KTD',
            'risk_grade' => 'MERAH',
            'status' => 'reported',
            'sla_due_at' => '2026-10-04 10:00:00', // 2026-08-20 + 45 hari
        ]);
    }

    /**
     * (b) Gerbang bisnis utama modul ini: kalkulasi matriks risiko 5x5 dan
     * jendela SLA harus persis mengikuti boundary yang disepakati.
     */
    public function test_risk_matrix_boundaries_and_sla_windows_are_correct(): void
    {
        $cases = [
            // skor 1-3 → BIRU
            [1, 1, 'BIRU'],
            [3, 1, 'BIRU'],
            [1, 3, 'BIRU'],
            // skor 4-6, dampak kecil/moderat → HIJAU
            [2, 2, 'HIJAU'], // 4
            [2, 3, 'HIJAU'], // 6
            [3, 2, 'HIJAU'], // 6
            [1, 5, 'HIJAU'], // 5 (probabilitas tinggi tapi dampak minimal)
            // severity-first: skor <= 6 tapi impact >= 4 dinaikkan ke KUNING
            [4, 1, 'KUNING'],
            [5, 1, 'KUNING'],
            // skor 8-12 → KUNING
            [3, 3, 'KUNING'], // 9
            [4, 3, 'KUNING'], // 12
            // skor 15-25 → MERAH
            [3, 5, 'MERAH'], // 15
            [4, 4, 'MERAH'], // 16
            [5, 5, 'MERAH'], // 25
        ];

        foreach ($cases as [$impact, $probability, $expectedGrade]) {
            $this->assertSame(
                $expectedGrade,
                IncidentReportService::gradeFromScores($impact, $probability),
                "Matriks {$impact}x{$probability} harus {$expectedGrade}.",
            );
        }

        $occurred = \Illuminate\Support\Carbon::parse('2026-08-20 10:00:00');

        // BIRU/HIJAU → +14 hari; KUNING/MERAH → +45 hari.
        $this->assertSame(
            '2026-09-03 10:00:00',
            IncidentReportService::slaDueAtFor('BIRU', 'KPC', $occurred)->format('Y-m-d H:i:s'),
        );
        $this->assertSame(
            '2026-09-03 10:00:00',
            IncidentReportService::slaDueAtFor('HIJAU', 'KNC', $occurred)->format('Y-m-d H:i:s'),
        );
        $this->assertSame(
            '2026-10-04 10:00:00',
            IncidentReportService::slaDueAtFor('KUNING', 'KTC', $occurred)->format('Y-m-d H:i:s'),
        );
        $this->assertSame(
            '2026-10-04 10:00:00',
            IncidentReportService::slaDueAtFor('MERAH', 'KTD', $occurred)->format('Y-m-d H:i:s'),
        );

        // SENTINEL selalu jalur 45 hari walau skornya BIRU.
        $this->assertSame(
            '2026-10-04 10:00:00',
            IncidentReportService::slaDueAtFor('BIRU', 'SENTINEL', $occurred)->format('Y-m-d H:i:s'),
        );
    }

    /** (b) End-to-end: grade & SLA ikut terhitung ulang saat update skor. */
    public function test_update_recomputes_grade_and_sla(): void
    {
        $this->actingUser();
        $report = $this->postJson('/api/v1/incident-reports', $this->storePayload([
            'impact_score' => 2,
            'probability_score' => 1,
        ]))->assertCreated()->json('data.id');

        // 2 x 1 = 2 → BIRU (+14 hari).
        $this->putJson("/api/v1/incident-reports/{$report}", [
            'impact_score' => 5,
            'probability_score' => 5,
        ])->assertOk()->assertJsonPath('data.risk_grade', 'MERAH');

        $this->assertDatabaseHas('incident_reports', [
            'id' => $report,
            'risk_grade' => 'MERAH',
            'sla_due_at' => '2026-10-04 10:00:00',
        ]);
    }

    /** (b) State machine: laporan belum diselidiki tidak boleh langsung ditutup. */
    public function test_report_cannot_be_closed_before_investigation(): void
    {
        $this->actingUser();
        $report = IncidentReport::factory()->create(['status' => IncidentReport::STATUS_REPORTED]);

        $this->postJson("/api/v1/incident-reports/{$report->id}/close")->assertStatus(422);

        $this->postJson("/api/v1/incident-reports/{$report->id}/investigate")
            ->assertOk()
            ->assertJsonPath('data.status', 'under_investigation');

        $this->postJson("/api/v1/incident-reports/{$report->id}/rca")
            ->assertOk()
            ->assertJsonPath('data.status', 'rca_required');

        $this->postJson("/api/v1/incident-reports/{$report->id}/close")
            ->assertOk()
            ->assertJsonPath('data.status', 'closed');
    }

    public function test_store_requires_valid_scores_and_category(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/incident-reports', $this->storePayload([
            'incident_category' => 'XXX',
            'impact_score' => 9,
        ]))->assertStatus(422);
    }
}
