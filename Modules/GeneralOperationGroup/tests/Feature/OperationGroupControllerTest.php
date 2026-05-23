<?php

namespace Modules\GeneralOperationGroup\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralOperationGroup\Models\OperationGroup;
use Tests\TestCase;

class OperationGroupControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_operation_group(): void
    {
        $this->actingUser();
        OperationGroup::factory()->count(3)->create();

        $this->getJson('/api/v1/operation-groups')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_operation_group(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/operation-groups', ['name' => 'Contoh Kelompokoperasi', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Kelompokoperasi');

        $this->assertDatabaseHas('operation_groups', ['name' => 'Contoh Kelompokoperasi']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        OperationGroup::factory()->create(['name' => 'Contoh Kelompokoperasi']);

        $this->postJson('/api/v1/operation-groups', ['name' => 'Contoh Kelompokoperasi'])->assertStatus(422);
    }

    public function test_it_deletes_operation_group(): void
    {
        $this->actingUser();
        $operationGroup = OperationGroup::factory()->create();

        $this->deleteJson("/api/v1/operation-groups/{$operationGroup->id}")->assertStatus(204);
        $this->assertDatabaseMissing('operation_groups', ['id' => $operationGroup->id]);
    }
}