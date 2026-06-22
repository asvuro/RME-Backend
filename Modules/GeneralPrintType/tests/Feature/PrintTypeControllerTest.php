<?php

namespace Modules\GeneralPrintType\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralPrintType\Models\PrintType;
use Tests\TestCase;

class PrintTypeControllerTest extends TestCase
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

    public function test_it_lists_print_type(): void
    {
        $this->actingUser();
        PrintType::factory()->count(3)->create();

        $this->getJson('/api/v1/print-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_print_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/print-types', ['name' => 'Contoh Jeniscetak', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jeniscetak');

        $this->assertDatabaseHas('print_types', ['name' => 'Contoh Jeniscetak']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        PrintType::factory()->create(['name' => 'Contoh Jeniscetak']);

        $this->postJson('/api/v1/print-types', ['name' => 'Contoh Jeniscetak'])->assertStatus(422);
    }

    public function test_it_deletes_print_type(): void
    {
        $this->actingUser();
        $printType = PrintType::factory()->create();

        $this->deleteJson("/api/v1/print-types/{$printType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('print_types', ['id' => $printType->id]);
    }
}