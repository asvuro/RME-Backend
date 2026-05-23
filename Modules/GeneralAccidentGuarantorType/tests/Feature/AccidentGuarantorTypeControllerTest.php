<?php

namespace Modules\GeneralAccidentGuarantorType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralAccidentGuarantorType\Models\AccidentGuarantorType;
use Tests\TestCase;

class AccidentGuarantorTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_accident_guarantor_type(): void
    {
        $this->actingUser();
        AccidentGuarantorType::factory()->count(3)->create();

        $this->getJson('/api/v1/accident-guarantor-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_accident_guarantor_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/accident-guarantor-types', ['name' => 'Contoh Jenispenjaminkecelakaan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenispenjaminkecelakaan');

        $this->assertDatabaseHas('accident_guarantor_types', ['name' => 'Contoh Jenispenjaminkecelakaan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        AccidentGuarantorType::factory()->create(['name' => 'Contoh Jenispenjaminkecelakaan']);

        $this->postJson('/api/v1/accident-guarantor-types', ['name' => 'Contoh Jenispenjaminkecelakaan'])->assertStatus(422);
    }

    public function test_it_deletes_accident_guarantor_type(): void
    {
        $this->actingUser();
        $accidentGuarantorType = AccidentGuarantorType::factory()->create();

        $this->deleteJson("/api/v1/accident-guarantor-types/{$accidentGuarantorType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('accident_guarantor_types', ['id' => $accidentGuarantorType->id]);
    }
}