<?php

namespace Modules\AuditQualityIndicator\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AuditQualityIndicator\Models\QualityIndicator;
use Modules\AuditQualityIndicator\Models\QualityIndicatorRecord;
use Modules\Auth\Models\User;
use Tests\TestCase;

class QualityIndicatorApiTest extends TestCase
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

    public function test_petugas_bisa_menyimpan_indikator_mutu(): void
    {
        $this->actingPetugas();

        $this->postJson('/api/v1/quality-indicators', [
            'code' => 'INM-RJ-001',
            'name' => 'Kepatuhan cuci tangan',
            'unit_of_measure' => '%',
            'target_value' => 95,
            'category' => 'sasaran_keselamatan',
        ])
            ->assertCreated()
            ->assertJsonPath('code', 'INM-RJ-001')
            ->assertJsonPath('category', 'sasaran_keselamatan')
            ->assertJsonPath('target_value', '95.00');
    }

    public function test_kategori_diluar_daftar_ditolak(): void
    {
        $this->actingPetugas();

        $this->postJson('/api/v1/quality-indicators', [
            'code' => 'INM-BAD-01',
            'name' => 'Kategori tidak sah',
            'unit_of_measure' => '%',
            'category' => 'keuangan',
        ])->assertStatus(422);
    }

    public function test_hapus_indikator_ditolak_bila_masih_punya_catatan_capaian(): void
    {
        $this->actingPetugas();

        $indicator = QualityIndicator::factory()->create();
        QualityIndicatorRecord::factory()->create([
            'indicator_id' => $indicator->id,
            'period_month' => now()->month,
        ]);

        // Gerbang bisnis master: riwayat capaian adalah bukti akreditasi,
        // tidak boleh ikut terhapus bersama definisi indikatornya.
        $this->deleteJson("/api/v1/quality-indicators/{$indicator->id}")
            ->assertStatus(422);

        $this->assertDatabaseHas('quality_indicators', ['id' => $indicator->id]);
    }

    public function test_index_menampilkan_daftar_dengan_filter_kategori(): void
    {
        $this->actingPetugas();

        QualityIndicator::factory()->create(['code' => 'INM-KL-002', 'category' => QualityIndicator::CATEGORY_KLINIS]);
        QualityIndicator::factory()->create(['code' => 'INM-MN-001', 'category' => QualityIndicator::CATEGORY_MANAJERIAL]);
        QualityIndicator::factory()->create(['code' => 'INM-KL-001', 'category' => QualityIndicator::CATEGORY_KLINIS]);

        $this->getJson('/api/v1/quality-indicators?category=klinis')
            ->assertOk()
            ->assertJsonCount(2, 'data');

        // Urutan memakai kode: INM-KL-001 sebelum INM-KL-002.
        $this->getJson('/api/v1/quality-indicators')
            ->assertOk()
            ->assertJsonPath('data.0.code', 'INM-KL-001');
    }
}
