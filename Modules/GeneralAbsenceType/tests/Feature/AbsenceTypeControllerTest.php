<?php

namespace Modules\GeneralAbsenceType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralAbsenceType\Models\AbsenceType;
use Tests\TestCase;

class AbsenceTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_absence_type(): void
    {
        $this->actingUser();
        AbsenceType::factory()->count(3)->create();

        $this->getJson('/api/v1/absence-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_absence_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/absence-types', ['name' => 'Contoh Jenisabsen', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenisabsen');

        $this->assertDatabaseHas('absence_types', ['name' => 'Contoh Jenisabsen']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        AbsenceType::factory()->create(['name' => 'Contoh Jenisabsen']);

        $this->postJson('/api/v1/absence-types', ['name' => 'Contoh Jenisabsen'])->assertStatus(422);
    }

    public function test_it_deletes_absence_type(): void
    {
        $this->actingUser();
        $absenceType = AbsenceType::factory()->create();

        $this->deleteJson("/api/v1/absence-types/{$absenceType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('absence_types', ['id' => $absenceType->id]);
    }
}