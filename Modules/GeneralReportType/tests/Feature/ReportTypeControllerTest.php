<?php

namespace Modules\GeneralReportType\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralReportType\Models\ReportType;
use Tests\TestCase;

class ReportTypeControllerTest extends TestCase
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

    public function test_it_lists_report_types(): void
    {
        $this->actingUser();
        ReportType::factory()->count(3)->create();

        $this->getJson('/api/v1/report-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_report_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/report-types', [
            'name' => 'Laporan Kunjungan',
            'class_name' => 'LaporanKunjungan',
            'module' => 'PENDAFTAR',
        ])
            ->assertCreated()
            ->assertJsonPath('name', 'Laporan Kunjungan');

        $this->assertDatabaseHas('report_types', ['name' => 'Laporan Kunjungan']);
    }

    public function test_it_rejects_missing_class_name(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/report-types', ['name' => 'Laporan Kunjungan', 'module' => 'PENDAFTAR'])
            ->assertStatus(422);
    }

    public function test_it_deletes_report_type(): void
    {
        $this->actingUser();
        $reportType = ReportType::factory()->create();

        $this->deleteJson("/api/v1/report-types/{$reportType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('report_types', ['id' => $reportType->id]);
    }
}
