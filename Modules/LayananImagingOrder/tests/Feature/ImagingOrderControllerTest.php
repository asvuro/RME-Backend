<?php

namespace Modules\LayananImagingOrder\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Models\Employee;
use Modules\LayananImagingOrder\Models\ImagingOrder;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

class ImagingOrderControllerTest extends TestCase
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

    private function validPayload(?int $visitId = null): array
    {
        return [
            'visit_id' => $visitId ?? Visit::factory()->create()->id,
            'modality' => 'CT',
            'body_part' => 'Thorax',
            'ordered_by' => Employee::factory()->create()->id,
            'ordered_at' => '2026-08-26 08:00:00',
        ];
    }

    /** (a) store berhasil dengan user berrole petugas. */
    public function test_petugas_can_create_imaging_order(): void
    {
        $this->actingUser();

        $response = $this->postJson('/api/v1/imaging-orders', $this->validPayload());

        $response->assertCreated()->assertJsonPath('data.status', 'ordered');
        $this->assertDatabaseHas('imaging_orders', [
            'modality' => 'CT',
            'body_part' => 'Thorax',
            'status' => ImagingOrder::STATUS_ORDERED,
        ]);
    }

    /** Status tidak boleh disuntik dari input pembuat (gerbang state machine). */
    public function test_create_ignores_injected_status(): void
    {
        $this->actingUser();

        $payload = $this->validPayload();
        $payload['status'] = ImagingOrder::STATUS_COMPLETED;

        $this->postJson('/api/v1/imaging-orders', $payload)
            ->assertCreated()
            ->assertJsonPath('data.status', ImagingOrder::STATUS_ORDERED);
    }

    /** (c) list/index terlihat dan filter per kunjungan bekerja. */
    public function test_index_lists_imaging_orders_with_visit_filter(): void
    {
        $this->actingUser();

        $visitA = Visit::factory()->create();
        ImagingOrder::factory()->count(2)->create(['visit_id' => $visitA->id]);
        ImagingOrder::factory()->count(3)->create();

        $this->getJson('/api/v1/imaging-orders')->assertOk()->assertJsonCount(5, 'data');
        $this->getJson("/api/v1/imaging-orders?visit_id={$visitA->id}")
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    /** (b) gerbang: order yang sudah selesai tidak dapat dijadwalkan ulang. */
    public function test_schedule_rejects_completed_order(): void
    {
        $this->actingUser();
        $order = ImagingOrder::factory()->completed()->create();

        $this->postJson("/api/v1/imaging-orders/{$order->id}/schedule", [
            'scheduled_at' => '2026-08-27 09:00:00',
        ])->assertStatus(422);

        $this->assertDatabaseHas('imaging_orders', [
            'id' => $order->id,
            'status' => ImagingOrder::STATUS_COMPLETED,
        ]);
    }

    /** (b) gerbang: order yang sudah selesai tidak dapat dibatalkan. */
    public function test_cancel_rejects_completed_order(): void
    {
        $this->actingUser();
        $order = ImagingOrder::factory()->completed()->create();

        $this->postJson("/api/v1/imaging-orders/{$order->id}/cancel")->assertStatus(422);

        $this->assertDatabaseHas('imaging_orders', [
            'id' => $order->id,
            'status' => ImagingOrder::STATUS_COMPLETED,
        ]);
    }

    /** (b) alur bahagia: ordered → scheduled → completed lewat pencatatan studi. */
    public function test_lifecycle_from_ordered_to_completed_via_study_recording(): void
    {
        $this->actingUser();

        $created = $this->postJson('/api/v1/imaging-orders', $this->validPayload())
            ->assertCreated()
            ->assertJsonPath('data.status', ImagingOrder::STATUS_ORDERED)
            ->json('data');

        $orderId = $created['id'];

        $this->postJson("/api/v1/imaging-orders/{$orderId}/schedule", [
            'scheduled_at' => '2026-08-27 09:00:00',
        ])->assertOk()->assertJsonPath('data.status', ImagingOrder::STATUS_SCHEDULED);

        // Pencatatan studi adalah gerbang penyelesaian: status order ikut berubah.
        $this->postJson('/api/v1/imaging-studies', [
            'imaging_order_id' => $orderId,
            'performed_at' => '2026-08-27 10:30:00',
            'findings_summary' => 'Tidak tampak lesi aktif.',
        ])->assertCreated();

        $this->assertDatabaseHas('imaging_studies', ['imaging_order_id' => $orderId]);
        $this->assertDatabaseHas('imaging_orders', [
            'id' => $orderId,
            'status' => ImagingOrder::STATUS_COMPLETED,
        ]);
    }
}
