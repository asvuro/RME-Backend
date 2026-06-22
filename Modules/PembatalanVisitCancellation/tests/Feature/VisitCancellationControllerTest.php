<?php

namespace Modules\PembatalanVisitCancellation\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\PembatalanVisitCancellation\Models\VisitCancellation;
use Tests\TestCase;

class VisitCancellationControllerTest extends TestCase
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

    public function test_it_lists_visit_cancellations(): void
    {
        $this->actingUser();
        VisitCancellation::factory()->count(3)->create();

        $this->getJson('/api/v1/pembatalan-visit-cancellations')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_visit_cancellation(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/pembatalan-visit-cancellations', [
            'visit_id' => \Modules\PendaftaranVisit\Models\Visit::factory()->create()->id,
            'cancelled_by' => \Modules\Auth\Models\User::factory()->create()->id,
            'reason' => 'Test description text',
            'cancelled_at' => '2026-01-01 08:00:00',
        ])->assertCreated();

        $this->assertDatabaseCount('pembatalan_visit_cancellations', 1);
    }

}
