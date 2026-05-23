<?php

namespace Modules\GeneralMixturePackagingType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralMixturePackagingType\Models\MixturePackagingType;
use Tests\TestCase;

class MixturePackagingTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_mixture_packaging_type(): void
    {
        $this->actingUser();
        MixturePackagingType::factory()->count(3)->create();

        $this->getJson('/api/v1/mixture-packaging-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_mixture_packaging_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/mixture-packaging-types', ['name' => 'Contoh Jeniskemasanracikan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jeniskemasanracikan');

        $this->assertDatabaseHas('mixture_packaging_types', ['name' => 'Contoh Jeniskemasanracikan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        MixturePackagingType::factory()->create(['name' => 'Contoh Jeniskemasanracikan']);

        $this->postJson('/api/v1/mixture-packaging-types', ['name' => 'Contoh Jeniskemasanracikan'])->assertStatus(422);
    }

    public function test_it_deletes_mixture_packaging_type(): void
    {
        $this->actingUser();
        $mixturePackagingType = MixturePackagingType::factory()->create();

        $this->deleteJson("/api/v1/mixture-packaging-types/{$mixturePackagingType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('mixture_packaging_types', ['id' => $mixturePackagingType->id]);
    }
}