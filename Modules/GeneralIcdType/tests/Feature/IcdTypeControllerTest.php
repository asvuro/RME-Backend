<?php

namespace Modules\GeneralIcdType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralIcdType\Models\IcdType;
use Tests\TestCase;

class IcdTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_icd_type(): void
    {
        $this->actingUser();
        IcdType::factory()->count(3)->create();

        $this->getJson('/api/v1/icd-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_icd_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/icd-types', ['name' => 'Contoh Jenisicd', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenisicd');

        $this->assertDatabaseHas('icd_types', ['name' => 'Contoh Jenisicd']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        IcdType::factory()->create(['name' => 'Contoh Jenisicd']);

        $this->postJson('/api/v1/icd-types', ['name' => 'Contoh Jenisicd'])->assertStatus(422);
    }

    public function test_it_deletes_icd_type(): void
    {
        $this->actingUser();
        $icdType = IcdType::factory()->create();

        $this->deleteJson("/api/v1/icd-types/{$icdType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('icd_types', ['id' => $icdType->id]);
    }
}