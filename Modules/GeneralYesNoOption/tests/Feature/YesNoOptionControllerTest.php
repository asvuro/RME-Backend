<?php

namespace Modules\GeneralYesNoOption\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralYesNoOption\Models\YesNoOption;
use Tests\TestCase;

class YesNoOptionControllerTest extends TestCase
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

    public function test_it_lists_yes_no_option(): void
    {
        $this->actingUser();
        YesNoOption::factory()->count(3)->create();

        $this->getJson('/api/v1/yes-no-options')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_yes_no_option(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/yes-no-options', ['name' => 'Contoh Yatidak', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Yatidak');

        $this->assertDatabaseHas('yes_no_options', ['name' => 'Contoh Yatidak']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        YesNoOption::factory()->create(['name' => 'Contoh Yatidak']);

        $this->postJson('/api/v1/yes-no-options', ['name' => 'Contoh Yatidak'])->assertStatus(422);
    }

    public function test_it_deletes_yes_no_option(): void
    {
        $this->actingUser();
        $yesNoOption = YesNoOption::factory()->create();

        $this->deleteJson("/api/v1/yes-no-options/{$yesNoOption->id}")->assertStatus(204);
        $this->assertDatabaseMissing('yes_no_options', ['id' => $yesNoOption->id]);
    }
}