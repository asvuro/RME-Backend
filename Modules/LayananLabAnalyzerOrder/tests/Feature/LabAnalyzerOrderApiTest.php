<?php

namespace Modules\LayananLabAnalyzerOrder\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Models\Employee;
use Modules\LayananLabAnalyzerOrder\Models\LabAnalyzerVendor;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

class LabAnalyzerOrderApiTest extends TestCase
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

    public function test_petugas_can_store_analyzer_order(): void
    {
        $this->actingPetugas();
        $visit = Visit::factory()->create();
        $doctor = Employee::factory()->create();
        $vendor = LabAnalyzerVendor::factory()->create(['vendor_name' => 'Novanet']);

        $response = $this->postJson('/api/v1/lab-analyzer-orders', [
            'visit_id' => $visit->id,
            'vendor_id' => $vendor->id,
            'test_code' => 'HBA1C',
            'ordered_by' => $doctor->id,
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.test_code', 'HBA1C');
        // Order baru WAJIB lahir sebagai 'ordered' - titik awal state machine.
        $response->assertJsonPath('data.status', 'ordered');
        $response->assertJsonPath('data.vendor.vendor_name', 'Novanet');

        $this->assertDatabaseHas('lab_analyzer_orders', [
            'visit_id' => $visit->id,
            'vendor_id' => $vendor->id,
            'test_code' => 'HBA1C',
            'ordered_by' => $doctor->id,
            'status' => 'ordered',
            'verified_by' => null,
        ]);
    }

    public function test_store_cannot_inject_status_from_client(): void
    {
        $this->actingPetugas();
        $visit = Visit::factory()->create();
        $doctor = Employee::factory()->create();

        // 'status' tidak fillable dan tidak divalidasi: suntikan 'verified'
        // dari body harus diabaikan (validated() membuang field tanpa rule),
        // bukan diterima - kalau tidak, seluruh gerbang bisa dilompati.
        $this->postJson('/api/v1/lab-analyzer-orders', [
            'visit_id' => $visit->id,
            'test_code' => 'CBC',
            'ordered_by' => $doctor->id,
            'status' => 'verified',
        ])->assertCreated()
            ->assertJsonPath('data.status', 'ordered');
    }
}
