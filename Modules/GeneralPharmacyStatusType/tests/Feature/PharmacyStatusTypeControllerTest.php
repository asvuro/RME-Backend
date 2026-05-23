<?php

namespace Modules\GeneralPharmacyStatusType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralPharmacyStatusType\Models\PharmacyStatusType;
use Tests\TestCase;

class PharmacyStatusTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_pharmacy_status_type(): void
    {
        $this->actingUser();
        PharmacyStatusType::factory()->count(3)->create();

        $this->getJson('/api/v1/pharmacy-status-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_pharmacy_status_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/pharmacy-status-types', ['name' => 'Contoh Jenisstatuslayananfarmasi', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenisstatuslayananfarmasi');

        $this->assertDatabaseHas('pharmacy_status_types', ['name' => 'Contoh Jenisstatuslayananfarmasi']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        PharmacyStatusType::factory()->create(['name' => 'Contoh Jenisstatuslayananfarmasi']);

        $this->postJson('/api/v1/pharmacy-status-types', ['name' => 'Contoh Jenisstatuslayananfarmasi'])->assertStatus(422);
    }

    public function test_it_deletes_pharmacy_status_type(): void
    {
        $this->actingUser();
        $pharmacyStatusType = PharmacyStatusType::factory()->create();

        $this->deleteJson("/api/v1/pharmacy-status-types/{$pharmacyStatusType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('pharmacy_status_types', ['id' => $pharmacyStatusType->id]);
    }
}