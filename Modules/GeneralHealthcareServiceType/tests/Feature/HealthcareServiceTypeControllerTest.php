<?php

namespace Modules\GeneralHealthcareServiceType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralHealthcareServiceType\Models\HealthcareServiceType;
use Tests\TestCase;

class HealthcareServiceTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_healthcare_service_type(): void
    {
        $this->actingUser();
        HealthcareServiceType::factory()->count(3)->create();

        $this->getJson('/api/v1/healthcare-service-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_healthcare_service_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/healthcare-service-types', ['name' => 'Contoh Jenispelayanankesehatan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenispelayanankesehatan');

        $this->assertDatabaseHas('healthcare_service_types', ['name' => 'Contoh Jenispelayanankesehatan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        HealthcareServiceType::factory()->create(['name' => 'Contoh Jenispelayanankesehatan']);

        $this->postJson('/api/v1/healthcare-service-types', ['name' => 'Contoh Jenispelayanankesehatan'])->assertStatus(422);
    }

    public function test_it_deletes_healthcare_service_type(): void
    {
        $this->actingUser();
        $healthcareServiceType = HealthcareServiceType::factory()->create();

        $this->deleteJson("/api/v1/healthcare-service-types/{$healthcareServiceType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('healthcare_service_types', ['id' => $healthcareServiceType->id]);
    }
}