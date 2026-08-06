<?php

namespace Modules\LayananDrugInteractionCheck\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\InventoryItem\Database\Factories\ItemFactory;
use Modules\LayananDrugInteractionCheck\Models\DrugInteractionRule;
use Tests\TestCase;

class DrugInteractionRuleApiTest extends TestCase
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

    public function test_petugas_can_create_interaction_rule(): void
    {
        $this->actingUser();

        $itemA = ItemFactory::new()->create(['name' => 'Warfarin 5mg']);
        $itemB = ItemFactory::new()->create(['name' => 'Amoxicillin 500mg']);

        $this->postJson('/api/v1/drug-interaction-rules', [
            'item_id_a' => $itemA->id,
            'item_id_b' => $itemB->id,
            'severity' => 'major_contraindicated',
            'clinical_note' => 'Risiko perdarahan berat bila digabung antibiotik golongan ini.',
        ])->assertCreated()
            ->assertJsonPath('item_id_a', $itemA->id)
            ->assertJsonPath('item_id_b', $itemB->id)
            ->assertJsonPath('severity', 'major_contraindicated');

        $this->assertDatabaseCount('drug_interaction_rules', 1);
    }

    public function test_it_rejects_self_pair_and_duplicate_reversed_pair(): void
    {
        $this->actingUser();

        // Gerbang 1: pasangan tidak boleh berisi obat yang sama (validasi + service).
        $itemA = ItemFactory::new()->create();
        $itemB = ItemFactory::new()->create();

        DrugInteractionRule::factory()->create([
            'item_id_a' => $itemA->id,
            'item_id_b' => $itemB->id,
            'severity' => 'moderate',
        ]);

        // Gerbang 2: pasangan terbalik B-A dianggap duplikat dari A-B.
        $this->postJson('/api/v1/drug-interaction-rules', [
            'item_id_a' => $itemB->id,
            'item_id_b' => $itemA->id,
            'severity' => 'minor',
            'clinical_note' => 'duplikat terbalik',
        ])->assertStatus(422);

        $this->postJson('/api/v1/drug-interaction-rules', [
            'item_id_a' => $itemA->id,
            'item_id_b' => $itemA->id,
            'severity' => 'minor',
            'clinical_note' => 'obat sama',
        ])->assertStatus(422);

        $this->assertDatabaseCount('drug_interaction_rules', 1);
    }

    public function test_it_lists_interaction_rules(): void
    {
        $this->actingUser();

        DrugInteractionRule::factory()->count(2)->create();

        $this->getJson('/api/v1/drug-interaction-rules')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }
}
