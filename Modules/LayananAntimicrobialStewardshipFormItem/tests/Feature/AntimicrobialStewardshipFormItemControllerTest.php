<?php

namespace Modules\LayananAntimicrobialStewardshipFormItem\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\LayananAntimicrobialStewardshipFormItem\Models\AntimicrobialStewardshipFormItem;
use Tests\TestCase;

class AntimicrobialStewardshipFormItemControllerTest extends TestCase
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

    public function test_it_lists_amr_items(): void
    {
        $this->actingUser();
        AntimicrobialStewardshipFormItem::factory()->count(3)->create();

        $this->getJson('/api/v1/antimicrobial-stewardship-form-items')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_amr_item(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/antimicrobial-stewardship-form-items', [
            'antimicrobial_stewardship_form_id' => \Modules\LayananAntimicrobialStewardshipForm\Models\AntimicrobialStewardshipForm::factory()->create()->id,
            'dose' => 'Test Dose',
            'route' => 'Test Route',
            'frequency' => 'Test Frequency',
        ])->assertCreated();

        $this->assertDatabaseCount('antimicrobial_stewardship_form_items', 1);
    }

}
