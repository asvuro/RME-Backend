<?php

namespace Modules\GeneralAgeGroup\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralAgeGroup\Models\AgeGroup;
use Tests\TestCase;

class AgeGroupControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_age_group(): void
    {
        $this->actingUser();
        AgeGroup::factory()->count(3)->create();

        $this->getJson('/api/v1/age-groups')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_age_group(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/age-groups', ['name' => 'Contoh Kelompokumur', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Kelompokumur');

        $this->assertDatabaseHas('age_groups', ['name' => 'Contoh Kelompokumur']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        AgeGroup::factory()->create(['name' => 'Contoh Kelompokumur']);

        $this->postJson('/api/v1/age-groups', ['name' => 'Contoh Kelompokumur'])->assertStatus(422);
    }

    public function test_it_deletes_age_group(): void
    {
        $this->actingUser();
        $ageGroup = AgeGroup::factory()->create();

        $this->deleteJson("/api/v1/age-groups/{$ageGroup->id}")->assertStatus(204);
        $this->assertDatabaseMissing('age_groups', ['id' => $ageGroup->id]);
    }
}