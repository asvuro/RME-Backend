<?php

namespace Modules\LayananLabAnalyzerOrder\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\LayananLabAnalyzerOrder\Models\LabAnalyzerOrder;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

class LabAnalyzerOrderIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');
    }

    public function test_index_lists_all_analyzer_orders(): void
    {
        $visit = Visit::factory()->create();
        LabAnalyzerOrder::factory()->count(3)->create(['visit_id' => $visit->id]);

        $this->getJson('/api/v1/lab-analyzer-orders')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'visit_id', 'vendor_id', 'test_code', 'status', 'ordered_at'],
                ],
            ]);
    }

    public function test_index_filters_by_status(): void
    {
        LabAnalyzerOrder::factory()->count(2)->create();
        LabAnalyzerOrder::factory()->resultReceived()->create();

        $response = $this->getJson('/api/v1/lab-analyzer-orders?status=result_received')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->assertSame('result_received', $response->json('data.0.status'));
    }

    public function test_index_filters_by_visit(): void
    {
        $visitA = Visit::factory()->create();
        $visitB = Visit::factory()->create();
        LabAnalyzerOrder::factory()->count(2)->create(['visit_id' => $visitA->id]);
        LabAnalyzerOrder::factory()->create(['visit_id' => $visitB->id]);

        $response = $this->getJson("/api/v1/lab-analyzer-orders?visit_id={$visitB->id}")
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->assertSame($visitB->id, $response->json('data.0.visit_id'));
    }
}
