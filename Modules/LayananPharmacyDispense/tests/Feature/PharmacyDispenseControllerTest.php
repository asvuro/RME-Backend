<?php

namespace Modules\LayananPharmacyDispense\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\LayananPharmacyDispense\Models\PharmacyDispense;
use Tests\TestCase;

class PharmacyDispenseControllerTest extends TestCase
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

    public function test_it_lists_dispenses(): void
    {
        $this->actingUser();
        PharmacyDispense::factory()->count(3)->create();

        $this->getJson('/api/v1/pharmacy-dispenses')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_dispense(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/pharmacy-dispenses', [
            'prescription_id' => \Modules\LayananPrescription\Models\Prescription::factory()->create()->id,
            'quantity' => 5,
            'status' => 'pending',
        ])->assertCreated();

        $this->assertDatabaseCount('pharmacy_dispenses', 1);
    }

    public function test_it_shows_dispense(): void
    {
        $this->actingUser();
        $dispense = PharmacyDispense::factory()->create();

        $this->getJson("/api/v1/pharmacy-dispenses/{$dispense->id}")->assertOk()->assertJsonPath('data.id', $dispense->id);
    }

}
