<?php

namespace Modules\GeneralFamilyRelationship\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralFamilyRelationship\Models\FamilyRelationship;
use Tests\TestCase;

class FamilyRelationshipControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_family_relationship(): void
    {
        $this->actingUser();
        FamilyRelationship::factory()->count(3)->create();

        $this->getJson('/api/v1/family-relationships')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_family_relationship(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/family-relationships', ['name' => 'Contoh Statushubungandalamkeluarga', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Statushubungandalamkeluarga');

        $this->assertDatabaseHas('family_relationships', ['name' => 'Contoh Statushubungandalamkeluarga']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        FamilyRelationship::factory()->create(['name' => 'Contoh Statushubungandalamkeluarga']);

        $this->postJson('/api/v1/family-relationships', ['name' => 'Contoh Statushubungandalamkeluarga'])->assertStatus(422);
    }

    public function test_it_deletes_family_relationship(): void
    {
        $this->actingUser();
        $familyRelationship = FamilyRelationship::factory()->create();

        $this->deleteJson("/api/v1/family-relationships/{$familyRelationship->id}")->assertStatus(204);
        $this->assertDatabaseMissing('family_relationships', ['id' => $familyRelationship->id]);
    }
}