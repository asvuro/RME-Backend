<?php

namespace Modules\GeneralPainOnsetType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralPainOnsetType\Models\PainOnsetType;
use Tests\TestCase;

class PainOnsetTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_pain_onset_type(): void
    {
        $this->actingUser();
        PainOnsetType::factory()->count(3)->create();

        $this->getJson('/api/v1/pain-onset-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_pain_onset_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/pain-onset-types', ['name' => 'Contoh Jenisonsetpenilaiannyeri', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenisonsetpenilaiannyeri');

        $this->assertDatabaseHas('pain_onset_types', ['name' => 'Contoh Jenisonsetpenilaiannyeri']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        PainOnsetType::factory()->create(['name' => 'Contoh Jenisonsetpenilaiannyeri']);

        $this->postJson('/api/v1/pain-onset-types', ['name' => 'Contoh Jenisonsetpenilaiannyeri'])->assertStatus(422);
    }

    public function test_it_deletes_pain_onset_type(): void
    {
        $this->actingUser();
        $painOnsetType = PainOnsetType::factory()->create();

        $this->deleteJson("/api/v1/pain-onset-types/{$painOnsetType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('pain_onset_types', ['id' => $painOnsetType->id]);
    }
}