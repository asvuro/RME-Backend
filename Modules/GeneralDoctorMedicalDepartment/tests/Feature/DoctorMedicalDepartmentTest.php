<?php

namespace Modules\GeneralDoctorMedicalDepartment\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Modules\GeneralDoctorMedicalDepartment\Models\DoctorMedicalDepartment;
use Modules\GeneralDoctor\Models\Doctor;
use Modules\GeneralMedicalDepartment\Models\MedicalDepartment;

class DoctorMedicalDepartmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
        // Rute modul ini kini dilindungi auth:sanctum (fix temuan security
        // review K-1) - semua request test harus terautentikasi.
        $user = \Modules\Auth\Models\User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');
    }

    public function test_can_list_assignments()
    {
        DoctorMedicalDepartment::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/doctor-medical-departments');
        $response->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_can_create_assignment()
    {
        $doctor = Doctor::factory()->create();
        $dept = MedicalDepartment::factory()->create();
        $data = [
            'doctor_id' => $doctor->id,
            'medical_department_id' => $dept->id,
            'is_head' => true,
        ];
        $response = $this->postJson('/api/v1/doctor-medical-departments', $data);
        $response->assertCreated();
        $this->assertDatabaseHas('doctor_medical_departments', $data);
    }

    public function test_can_show_assignment()
    {
        $assignment = DoctorMedicalDepartment::factory()->create();
        $response = $this->getJson("/api/v1/doctor-medical-departments/{$assignment->id}");
        $response->assertOk()->assertJsonPath('data.id', $assignment->id);
    }

    public function test_can_update_assignment()
    {
        $assignment = DoctorMedicalDepartment::factory()->create(['is_head' => false]);
        $response = $this->putJson("/api/v1/doctor-medical-departments/{$assignment->id}", [
            'is_head' => true,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('doctor_medical_departments', [
            'id' => $assignment->id,
            'is_head' => true,
        ]);
    }

    public function test_can_delete_assignment()
    {
        $assignment = DoctorMedicalDepartment::factory()->create();
        $response = $this->deleteJson("/api/v1/doctor-medical-departments/{$assignment->id}");
        $response->assertNoContent();
        $this->assertDatabaseMissing('doctor_medical_departments', ['id' => $assignment->id]);
    }
}
