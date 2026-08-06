<?php

namespace Modules\LayananImagingOrder\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\LayananImagingOrder\Models\ImagingOrder;
use Modules\LayananImagingOrder\Models\ImagingStudy;
use Tests\TestCase;

class ImagingStudyControllerTest extends TestCase
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

    /** Studi tercatat + order yang belum selesai otomatis menjadi completed. */
    public function test_petugas_can_record_study_which_completes_the_order(): void
    {
        $this->actingUser();
        $order = ImagingOrder::factory()->scheduled()->create();

        $response = $this->postJson('/api/v1/imaging-studies', [
            'imaging_order_id' => $order->id,
            'performed_at' => '2026-08-26 10:00:00',
            'findings_summary' => 'Impresi pneumonia kanan.',
            'report_url' => '/storage/imaging/reports/1.pdf',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('imaging_studies', [
            'imaging_order_id' => $order->id,
            'report_url' => '/storage/imaging/reports/1.pdf',
        ]);
        $this->assertDatabaseHas('imaging_orders', [
            'id' => $order->id,
            'status' => ImagingOrder::STATUS_COMPLETED,
        ]);
    }

    /** (b) gerbang utama: studi tidak dapat dicatat pada order yang dibatalkan. */
    public function test_rejects_study_for_cancelled_order(): void
    {
        $this->actingUser();
        $order = ImagingOrder::factory()->cancelled()->create();

        $this->postJson('/api/v1/imaging-studies', [
            'imaging_order_id' => $order->id,
            'performed_at' => '2026-08-26 10:00:00',
        ])->assertStatus(422);

        $this->assertDatabaseCount('imaging_studies', 0);
        $this->assertDatabaseHas('imaging_orders', [
            'id' => $order->id,
            'status' => ImagingOrder::STATUS_CANCELLED,
        ]);
    }

    /** (c) list studi, termasuk filter per order. */
    public function test_index_lists_studies_filtered_by_order(): void
    {
        $this->actingUser();

        $orderA = ImagingOrder::factory()->create();
        ImagingStudy::factory()->count(2)->create(['imaging_order_id' => $orderA->id]);
        ImagingStudy::factory()->count(3)->create();

        $this->getJson('/api/v1/imaging-studies')->assertOk()->assertJsonCount(5, 'data');
        $this->getJson("/api/v1/imaging-studies?imaging_order_id={$orderA->id}")
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    /** Amendemen hasil diperbolehkan selama ordernya tidak dibatalkan. */
    public function test_can_update_findings_and_report_url(): void
    {
        $this->actingUser();
        $study = ImagingStudy::factory()->create(['study_instance_uid' => null]);

        $this->patchJson("/api/v1/imaging-studies/{$study->id}", [
            'findings_summary' => 'Koreksi: konsolidasi lobus bawah kanan.',
            'report_url' => '/storage/imaging/reports/9.pdf',
        ])->assertOk()
            ->assertJsonPath('data.report_url', '/storage/imaging/reports/9.pdf');
    }
}
