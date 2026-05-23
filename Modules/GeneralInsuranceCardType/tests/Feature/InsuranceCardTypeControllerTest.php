<?php

namespace Modules\GeneralInsuranceCardType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralInsuranceCardType\Models\InsuranceCardType;
use Tests\TestCase;

class InsuranceCardTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_insurance_card_type(): void
    {
        $this->actingUser();
        InsuranceCardType::factory()->count(3)->create();

        $this->getJson('/api/v1/insurance-card-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_insurance_card_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/insurance-card-types', ['name' => 'Contoh Jeniskartuasuransipenjamin', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jeniskartuasuransipenjamin');

        $this->assertDatabaseHas('insurance_card_types', ['name' => 'Contoh Jeniskartuasuransipenjamin']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        InsuranceCardType::factory()->create(['name' => 'Contoh Jeniskartuasuransipenjamin']);

        $this->postJson('/api/v1/insurance-card-types', ['name' => 'Contoh Jeniskartuasuransipenjamin'])->assertStatus(422);
    }

    public function test_it_deletes_insurance_card_type(): void
    {
        $this->actingUser();
        $insuranceCardType = InsuranceCardType::factory()->create();

        $this->deleteJson("/api/v1/insurance-card-types/{$insuranceCardType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('insurance_card_types', ['id' => $insuranceCardType->id]);
    }
}