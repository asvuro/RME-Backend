<?php

namespace Modules\GeneralOperationType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralOperationType\Models\OperationType;
use Tests\TestCase;

class OperationTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_operation_type(): void
    {
        $this->actingUser();
        OperationType::factory()->count(3)->create();

        $this->getJson('/api/v1/operation-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_operation_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/operation-types', ['name' => 'Contoh Jenisoperasi', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenisoperasi');

        $this->assertDatabaseHas('operation_types', ['name' => 'Contoh Jenisoperasi']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        OperationType::factory()->create(['name' => 'Contoh Jenisoperasi']);

        $this->postJson('/api/v1/operation-types', ['name' => 'Contoh Jenisoperasi'])->assertStatus(422);
    }

    public function test_it_deletes_operation_type(): void
    {
        $this->actingUser();
        $operationType = OperationType::factory()->create();

        $this->deleteJson("/api/v1/operation-types/{$operationType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('operation_types', ['id' => $operationType->id]);
    }
}