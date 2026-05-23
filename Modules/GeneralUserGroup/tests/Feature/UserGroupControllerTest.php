<?php

namespace Modules\GeneralUserGroup\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralUserGroup\Models\UserGroup;
use Tests\TestCase;

class UserGroupControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_user_group(): void
    {
        $this->actingUser();
        UserGroup::factory()->count(3)->create();

        $this->getJson('/api/v1/user-groups')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_user_group(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/user-groups', ['name' => 'Contoh Groupuser', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Groupuser');

        $this->assertDatabaseHas('user_groups', ['name' => 'Contoh Groupuser']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        UserGroup::factory()->create(['name' => 'Contoh Groupuser']);

        $this->postJson('/api/v1/user-groups', ['name' => 'Contoh Groupuser'])->assertStatus(422);
    }

    public function test_it_deletes_user_group(): void
    {
        $this->actingUser();
        $userGroup = UserGroup::factory()->create();

        $this->deleteJson("/api/v1/user-groups/{$userGroup->id}")->assertStatus(204);
        $this->assertDatabaseMissing('user_groups', ['id' => $userGroup->id]);
    }
}