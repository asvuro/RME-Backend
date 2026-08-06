<?php

namespace Modules\AuditQualityIndicator\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AuditQualityIndicator\Models\QualityIndicator;
use Modules\AuditQualityIndicator\Models\QualityIndicatorRecord;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Models\Employee;
use Tests\TestCase;

class QualityIndicatorRecordApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function actingPetugasDenganPegawai(): array
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user, 'sanctum');

        return [$user, $employee];
    }

    public function test_petugas_bisa_menyimpan_catatan_dan_tercatat_oleh_pegawai(): void
    {
        [, $employee] = $this->actingPetugasDenganPegawai();
        $indicator = QualityIndicator::factory()->create();

        // Rumus inti INM: 45 dari 50 = 90%.
        $response = $this->postJson('/api/v1/quality-indicator-records', [
            'indicator_id' => $indicator->id,
            'period_month' => now()->month,
            'period_year' => now()->year,
            'numerator' => 45,
            'denominator' => 50,
        ]);

        $response->assertCreated();
        $this->assertSame(90.0, (float) $response->json('achieved_value'));
        // recorded_by otomatis mengikuti profil pegawai petugas yang login.
        $this->assertSame($employee->id, $response->json('recorded_by'));
    }

    public function test_capaian_null_bila_denominator_nol(): void
    {
        $this->actingPetugasDenganPegawai();
        $indicator = QualityIndicator::factory()->create();

        $this->postJson('/api/v1/quality-indicator-records', [
            'indicator_id' => $indicator->id,
            'period_month' => now()->month,
            'period_year' => now()->year,
            'numerator' => 5,
            'denominator' => 0,
        ])
            ->assertCreated()
            ->assertJsonPath('achieved_value', null);
    }

    public function test_capaian_dibulatkan_dua_desimal(): void
    {
        $this->actingPetugasDenganPegawai();
        $indicator = QualityIndicator::factory()->create();

        // 1 dari 3 = 33,333...% -> dibulatkan 33.33.
        $response = $this->postJson('/api/v1/quality-indicator-records', [
            'indicator_id' => $indicator->id,
            'period_month' => now()->month,
            'period_year' => now()->year,
            'numerator' => 1,
            'denominator' => 3,
        ]);

        $response->assertCreated();
        $this->assertSame(33.33, (float) $response->json('achieved_value'));
    }

    public function test_periode_duplikat_pada_indikator_sama_ditolak(): void
    {
        $this->actingPetugasDenganPegawai();
        $indicator = QualityIndicator::factory()->create();

        $payload = [
            'indicator_id' => $indicator->id,
            'period_month' => now()->month,
            'period_year' => now()->year,
            'numerator' => 10,
            'denominator' => 20,
        ];

        $this->postJson('/api/v1/quality-indicator-records', $payload)->assertCreated();

        // Gerbang bisnis: satu catatan per indikator per bulan.
        $this->postJson('/api/v1/quality-indicator-records', $payload)->assertStatus(422);
    }

    public function test_periode_masa_depan_ditolak(): void
    {
        $this->actingPetugasDenganPegawai();
        $indicator = QualityIndicator::factory()->create();

        $this->postJson('/api/v1/quality-indicator-records', [
            'indicator_id' => $indicator->id,
            'period_month' => 12,
            'period_year' => now()->year + 1,
            'numerator' => 10,
            'denominator' => 20,
        ])->assertStatus(422);
    }

    public function test_index_catatan_terurut_periode(): void
    {
        $this->actingPetugasDenganPegawai();
        $indicator = QualityIndicator::factory()->create();

        QualityIndicatorRecord::factory()->create([
            'indicator_id' => $indicator->id,
            'period_month' => 3,
            'period_year' => 2026,
        ]);
        QualityIndicatorRecord::factory()->create([
            'indicator_id' => $indicator->id,
            'period_month' => 1,
            'period_year' => 2026,
        ]);

        $this->getJson("/api/v1/quality-indicator-records?indicator_id={$indicator->id}&year=2026")
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.period_month', 1)
            ->assertJsonPath('data.1.period_month', 3);
    }

    public function test_trend_mengembalikan_rekaman_tahun_diminta_urut_bulan(): void
    {
        $this->actingPetugasDenganPegawai();
        $indicator = QualityIndicator::factory()->create();

        foreach ([2, 1] as $month) {
            QualityIndicatorRecord::factory()->create([
                'indicator_id' => $indicator->id,
                'period_month' => $month,
                'period_year' => 2026,
                'numerator' => 40 * $month,
                'denominator' => 50,
            ]);
        }
        // Tahun lain tidak boleh ikut ke tren 2026.
        QualityIndicatorRecord::factory()->create([
            'indicator_id' => $indicator->id,
            'period_month' => 4,
            'period_year' => 2025,
        ]);

        $response = $this->getJson("/api/v1/quality-indicators/{$indicator->id}/trend?year=2026")
            ->assertOk()
            ->assertJsonPath('meta.year', 2026);

        $months = array_column($response->json('data'), 'period_month');
        $this->assertSame([1, 2], $months);

        // Bulan 1: 40/50 = 80%.
        $this->assertSame(80.0, (float) $response->json('data.0.achieved_value'));
    }
}
