<?php

namespace Modules\GeneralPainScaleMethod\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralPainScaleMethod\Models\PainScaleMethod;
use Tests\TestCase;

class PainScaleMethodControllerTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }
    private function actingUser(): void
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');
    }

    public function test_it_lists_pain_scale_method(): void
    {
        $this->actingUser();
        PainScaleMethod::factory()->count(3)->create();

        $this->getJson('/api/v1/pain-scale-methods')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_pain_scale_method(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/pain-scale-methods', ['name' => 'Contoh Metodeskalanyeri', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Metodeskalanyeri');

        $this->assertDatabaseHas('pain_scale_methods', ['name' => 'Contoh Metodeskalanyeri']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        PainScaleMethod::factory()->create(['name' => 'Contoh Metodeskalanyeri']);

        $this->postJson('/api/v1/pain-scale-methods', ['name' => 'Contoh Metodeskalanyeri'])->assertStatus(422);
    }

    public function test_it_deletes_pain_scale_method(): void
    {
        $this->actingUser();
        $painScaleMethod = PainScaleMethod::factory()->create();

        $this->deleteJson("/api/v1/pain-scale-methods/{$painScaleMethod->id}")->assertStatus(204);
        $this->assertDatabaseMissing('pain_scale_methods', ['id' => $painScaleMethod->id]);
    }
}