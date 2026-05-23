<?php

namespace Modules\GeneralOperationClass\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralOperationClass\Models\OperationClass;
use Tests\TestCase;

class OperationClassControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_operation_classe(): void
    {
        $this->actingUser();
        OperationClass::factory()->count(3)->create();

        $this->getJson('/api/v1/operation-classes')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_operation_class(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/operation-classes', ['name' => 'Contoh Golonganoperasi', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Golonganoperasi');

        $this->assertDatabaseHas('operation_classes', ['name' => 'Contoh Golonganoperasi']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        OperationClass::factory()->create(['name' => 'Contoh Golonganoperasi']);

        $this->postJson('/api/v1/operation-classes', ['name' => 'Contoh Golonganoperasi'])->assertStatus(422);
    }

    public function test_it_deletes_operation_class(): void
    {
        $this->actingUser();
        $operationClass = OperationClass::factory()->create();

        $this->deleteJson("/api/v1/operation-classes/{$operationClass->id}")->assertStatus(204);
        $this->assertDatabaseMissing('operation_classes', ['id' => $operationClass->id]);
    }
}