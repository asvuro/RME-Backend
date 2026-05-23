<?php

namespace Modules\GeneralCardType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralCardType\Models\CardType;
use Tests\TestCase;

class CardTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_card_type(): void
    {
        $this->actingUser();
        CardType::factory()->count(3)->create();

        $this->getJson('/api/v1/card-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_card_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/card-types', ['name' => 'Contoh Jeniskartudebitkredit', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jeniskartudebitkredit');

        $this->assertDatabaseHas('card_types', ['name' => 'Contoh Jeniskartudebitkredit']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        CardType::factory()->create(['name' => 'Contoh Jeniskartudebitkredit']);

        $this->postJson('/api/v1/card-types', ['name' => 'Contoh Jeniskartudebitkredit'])->assertStatus(422);
    }

    public function test_it_deletes_card_type(): void
    {
        $this->actingUser();
        $cardType = CardType::factory()->create();

        $this->deleteJson("/api/v1/card-types/{$cardType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('card_types', ['id' => $cardType->id]);
    }
}