<?php

namespace Modules\GeneralBank\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralBank\Models\Bank;
use Tests\TestCase;

class BankControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_bank(): void
    {
        $this->actingUser();
        Bank::factory()->count(3)->create();

        $this->getJson('/api/v1/banks')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_bank(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/banks', ['name' => 'Contoh Referensi', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Referensi');

        $this->assertDatabaseHas('banks', ['name' => 'Contoh Referensi']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        Bank::factory()->create(['name' => 'Contoh Referensi']);

        $this->postJson('/api/v1/banks', ['name' => 'Contoh Referensi'])->assertStatus(422);
    }

    public function test_it_deletes_bank(): void
    {
        $this->actingUser();
        $bank = Bank::factory()->create();

        $this->deleteJson("/api/v1/banks/{$bank->id}")->assertStatus(204);
        $this->assertDatabaseMissing('banks', ['id' => $bank->id]);
    }
}