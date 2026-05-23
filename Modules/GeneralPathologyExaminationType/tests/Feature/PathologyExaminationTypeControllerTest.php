<?php

namespace Modules\GeneralPathologyExaminationType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralPathologyExaminationType\Models\PathologyExaminationType;
use Tests\TestCase;

class PathologyExaminationTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_pathology_examination_type(): void
    {
        $this->actingUser();
        PathologyExaminationType::factory()->count(3)->create();

        $this->getJson('/api/v1/pathology-examination-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_pathology_examination_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/pathology-examination-types', ['name' => 'Contoh Jenispemeriksaanpa', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenispemeriksaanpa');

        $this->assertDatabaseHas('pathology_examination_types', ['name' => 'Contoh Jenispemeriksaanpa']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        PathologyExaminationType::factory()->create(['name' => 'Contoh Jenispemeriksaanpa']);

        $this->postJson('/api/v1/pathology-examination-types', ['name' => 'Contoh Jenispemeriksaanpa'])->assertStatus(422);
    }

    public function test_it_deletes_pathology_examination_type(): void
    {
        $this->actingUser();
        $pathologyExaminationType = PathologyExaminationType::factory()->create();

        $this->deleteJson("/api/v1/pathology-examination-types/{$pathologyExaminationType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('pathology_examination_types', ['id' => $pathologyExaminationType->id]);
    }
}